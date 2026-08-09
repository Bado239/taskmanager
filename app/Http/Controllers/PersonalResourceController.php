<?php

namespace App\Http\Controllers;

use App\Models\PersonalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalResourceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'author_or_source' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Insertion compatible PostgreSQL (évite le cast true → 1)
        DB::table('personal_resources')->insert([
            'type' => $request->type,
            'title' => $request->title,
            'author_or_source' => $request->author_or_source,
            'description' => $request->description,
            'status' => $request->type === 'book' ? 'to_read' : 'to_do',
            'is_active' => DB::raw('true'), // ← crucial pour PostgreSQL
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('dashboard', ['view' => 'personal'])
            ->with('success', 'Ressource personnelle ajoutée avec succès !');
    }

    public function destroy($id)
    {
        PersonalResource::findOrFail($id)->delete();

        return redirect()
            ->route('dashboard', ['view' => 'personal'])
            ->with('success', 'Ressource supprimée.');
    }
}