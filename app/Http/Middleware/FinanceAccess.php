<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceAccess
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $unlockedAt = $request->session()->get(
            'finances_unlocked_at'
        );

        $duration = (int) config('finance.lock_minutes', 30);

        $valid = is_numeric($unlockedAt)
            && (time() - (int) $unlockedAt) >= 0
            && (time() - (int) $unlockedAt) < ($duration * 60);

        if (!$valid) {

            $request->session()->forget(
                'finances_unlocked_at'
            );

            return redirect()->guest(
                route('finances.unlock')
            );
        }

        return $next($request);
    }
}
