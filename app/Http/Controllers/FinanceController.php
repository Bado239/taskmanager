<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FinanceController extends Controller
{
    private function defaultCategories(): array
    {
        return [
            ['Loyer', 'expense', 80000],
            ['Alimentation', 'expense', 75000],
            ['Transport', 'expense', 30000],
            ['Eau, électricité, téléphone, internet', 'expense', 20000],
            ['Aide familiale', 'expense', 50000],
            ['Santé et pharmacie', 'expense', 15000],
            ['Loisirs et sorties', 'expense', 15000],
            ['Habillement', 'expense', 10000],
            ['Petits imprévus courants', 'expense', 15000],
            ['Formation et compétences', 'expense', 10000],
            ['Construction', 'expense', 0],
            ['Épargne de sécurité', 'saving', 60000],
            ['Épargne projet immobilier', 'saving', 120000],
        ];
    }

    private function resolveMonth(?string $month): string
    {
        $month = $month ?: now('Africa/Dakar')->format('Y-m');

        if (
            !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)
            || !checkdate(
                (int) substr($month, 5, 2),
                1,
                (int) substr($month, 0, 4)
            )
        ) {
            throw ValidationException::withMessages([
                'month' => 'Le mois sélectionné est invalide.',
            ]);
        }

        return $month;
    }

    private function findMonth(string $month)
    {
        return DB::table('finance_months')
            ->where('month', $month)
            ->first();
    }

    private function getOrCreateMonth(string $month)
    {
        return DB::transaction(function () use ($month) {
            $existing = $this->findMonth($month);

            if ($existing) {
                return $existing;
            }

            // Copier le budget du mois antérieur le plus récent.
            $previous = DB::table('finance_months')
                ->where('month', '<', $month)
                ->orderByDesc('month')
                ->first();

            $id = DB::table('finance_months')->insertGetId([
                'month' => $month,
                'income' => $previous?->income ?? 500000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $categories = $previous
                ? DB::table('finance_categories')
                    ->where('finance_month_id', $previous->id)
                    ->orderBy('position')
                    ->get()
                    ->map(fn ($item) => [
                        $item->name,
                        $item->kind,
                        $item->planned_amount,
                    ])
                    ->all()
                : $this->defaultCategories();

            foreach ($categories as $position => $category) {
                DB::table('finance_categories')->insert([
                    'finance_month_id' => $id,
                    'name' => $category[0],
                    'kind' => $category[1],
                    'planned_amount' => $category[2],
                    'position' => $position,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $this->findMonth($month);
        });
    }

    private function getCategory(int $id, string $month)
    {
        return DB::table('finance_categories as c')
            ->join(
                'finance_months as m',
                'm.id',
                '=',
                'c.finance_month_id'
            )
            ->where('c.id', $id)
            ->where('m.month', $month)
            ->select('c.*')
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $month = $this->resolveMonth(
            $request->query('month')
        );

        $financeMonth = $this->getOrCreateMonth($month);

        $categories = DB::table('finance_categories as c')
            ->leftJoin('finance_movements as f', function ($join) {
                $join->on(
                    'f.finance_category_id',
                    '=',
                    'c.id'
                );
            })
            ->where('c.finance_month_id', $financeMonth->id)
            ->select(
                'c.id',
                'c.name',
                'c.kind',
                'c.planned_amount',
                'c.position'
            )
            ->selectRaw(
                'COALESCE(SUM(f.amount), 0) AS actual_amount'
            )
            ->groupBy(
                'c.id',
                'c.name',
                'c.kind',
                'c.planned_amount',
                'c.position'
            )
            ->orderBy('c.position')
            ->get();

        $movements = DB::table('finance_movements as f')
            ->join(
                'finance_categories as c',
                'c.id',
                '=',
                'f.finance_category_id'
            )
            ->where('c.finance_month_id', $financeMonth->id)
            ->select(
                'f.id',
                'f.amount',
                'f.movement_date',
                'f.description',
                'c.name as category_name',
                'c.kind'
            )
            ->orderByDesc('f.movement_date')
            ->orderByDesc('f.id')
            ->get();

        $plannedExpenses = $categories
            ->where('kind', 'expense')
            ->sum('planned_amount');

        $plannedSavings = $categories
            ->where('kind', 'saving')
            ->sum('planned_amount');

        $actualExpenses = $categories
            ->where('kind', 'expense')
            ->sum('actual_amount');

        $actualSavings = $categories
            ->where('kind', 'saving')
            ->sum('actual_amount');

        $unallocated = $financeMonth->income
            - $plannedExpenses
            - $plannedSavings;

        $remaining = $financeMonth->income
            - $actualExpenses
            - $actualSavings;

        return view('finances.index', compact(
            'month',
            'financeMonth',
            'categories',
            'movements',
            'plannedExpenses',
            'plannedSavings',
            'actualExpenses',
            'actualSavings',
            'unallocated',
            'remaining'
        ));
    }

    public function updateIncome(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
            'income' => 'required|integer|min:0|max:999999999999',
        ]);

        $month = $this->resolveMonth($data['month']);
        $financeMonth = $this->getOrCreateMonth($month);

        DB::table('finance_months')
            ->where('id', $financeMonth->id)
            ->update([
                'income' => $data['income'],
                'updated_at' => now(),
            ]);

        return $this->backToMonth(
            $month,
            'Revenu mensuel mis à jour.'
        );
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
            'name' => 'required|string|max:120',
            'kind' => 'required|in:expense,saving',
            'planned_amount' => 'required|integer|min:0|max:999999999999',
        ]);

        $month = $this->resolveMonth($data['month']);
        $financeMonth = $this->getOrCreateMonth($month);

        $name = trim($data['name']);

        $exists = DB::table('finance_categories')
            ->where('finance_month_id', $financeMonth->id)
            ->where('kind', $data['kind'])
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => 'Ce poste existe déjà pour ce mois.',
            ]);
        }

        $position = DB::table('finance_categories')
            ->where('finance_month_id', $financeMonth->id)
            ->max('position');

        DB::table('finance_categories')->insert([
            'finance_month_id' => $financeMonth->id,
            'name' => $name,
            'kind' => $data['kind'],
            'planned_amount' => $data['planned_amount'],
            'position' => ($position ?? -1) + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->backToMonth(
            $month,
            'Poste budgétaire ajouté.'
        );
    }

    public function updateCategory(Request $request, int $id)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
            'planned_amount' => 'required|integer|min:0|max:999999999999',
        ]);

        $month = $this->resolveMonth($data['month']);
        $category = $this->getCategory($id, $month);

        DB::table('finance_categories')
            ->where('id', $category->id)
            ->update([
                'planned_amount' => $data['planned_amount'],
                'updated_at' => now(),
            ]);

        return $this->backToMonth(
            $month,
            'Budget modifié avec succès.'
        );
    }

    public function destroyCategory(Request $request, int $id)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = $this->resolveMonth($data['month']);
        $category = $this->getCategory($id, $month);

        // Ne jamais perdre des opérations historiques.
        $hasMovements = DB::table('finance_movements')
            ->where('finance_category_id', $category->id)
            ->exists();

        if ($hasMovements) {
            return $this->backToMonth(
                $month,
                'Suppression impossible : ce poste contient des opérations.',
                'error'
            );
        }

        DB::table('finance_categories')
            ->where('id', $category->id)
            ->delete();

        return $this->backToMonth(
            $month,
            'Poste supprimé.'
        );
    }

    public function storeMovement(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
            'category_id' => 'required|integer',
            'amount' => 'required|integer|min:1|max:999999999999',
            'movement_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $month = $this->resolveMonth($data['month']);

        $category = $this->getCategory(
            $data['category_id'],
            $month
        );

        if (substr($data['movement_date'], 0, 7) !== $month) {
            throw ValidationException::withMessages([
                'movement_date' =>
                    'La date doit appartenir au mois sélectionné.',
            ]);
        }

        DB::table('finance_movements')->insert([
            'finance_category_id' => $category->id,
            'amount' => $data['amount'],
            'movement_date' => $data['movement_date'],
            'description' => $data['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->backToMonth(
            $month,
            $category->kind === 'saving'
                ? 'Versement d’épargne enregistré.'
                : 'Dépense enregistrée.'
        );
    }

    public function destroyMovement(Request $request, int $id)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = $this->resolveMonth($data['month']);

        $movement = DB::table('finance_movements as f')
            ->join(
                'finance_categories as c',
                'c.id',
                '=',
                'f.finance_category_id'
            )
            ->join(
                'finance_months as m',
                'm.id',
                '=',
                'c.finance_month_id'
            )
            ->where('f.id', $id)
            ->where('m.month', $month)
            ->select('f.id')
            ->firstOrFail();

        DB::table('finance_movements')
            ->where('id', $movement->id)
            ->delete();

        return $this->backToMonth(
            $month,
            'Opération supprimée.'
        );
    }

    private function backToMonth(
        string $month,
        string $message,
        string $type = 'success'
    ) {
        return redirect()
            ->route('finances.index', ['month' => $month])
            ->with($type, $message);
    }
}
