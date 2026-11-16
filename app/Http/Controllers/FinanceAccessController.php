<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FinanceAccessController extends Controller
{
    public function show(Request $request)
    {
        if ($this->isUnlocked($request)) {
            return redirect()->route('finances.index');
        }

        return view('finances.unlock');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $hash = config('finance.password_hash');

        if (!is_string($hash) || $hash === '') {
            abort(503, 'Protection financière non configurée.');
        }

        if (!Hash::check($request->password, $hash)) {
            return back()->withErrors([
                'password' => 'Mot de passe incorrect.',
            ]);
        }

        // Renouveler l'identifiant de session.
        $request->session()->regenerate();

        $request->session()->put(
            'finances_unlocked_at',
            time()
        );

        return redirect()->intended(
            route('finances.index')
        );
    }

    public function lock(Request $request)
    {
        $request->session()->forget(
            'finances_unlocked_at'
        );

        return redirect()
            ->route('finances.unlock')
            ->with('success', 'Mes Finances est verrouillé.');
    }

    private function isUnlocked(Request $request): bool
    {
        $unlockedAt = $request->session()->get(
            'finances_unlocked_at'
        );

        $duration = (int) config('finance.lock_minutes', 30);

        return is_numeric($unlockedAt)
            && (time() - (int) $unlockedAt) >= 0
            && (time() - (int) $unlockedAt) < ($duration * 60);
    }
}
