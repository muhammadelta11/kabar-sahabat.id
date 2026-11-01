<?php

namespace App\Http\Middleware;

use App\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanSubmitArticles
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->canSubmitArticles()) {
            return $next($request);
        }

        if ($user?->isKontributor()) {
            return redirect()
                ->route('auth.pending')
                ->with('status', 'Akun kontributor Anda belum disetujui redaksi.');
        }

        // Staf panel: arahkan ke /admin (bukan form publik kontributor)
        if ($user?->role instanceof UserRole && $user->role->canAccessPanel()) {
            return redirect('/admin')
                ->with('status', 'Akun staf memakai panel admin untuk mengelola artikel.');
        }

        abort(403, 'Akun Anda belum dapat mengirim artikel.');
    }
}
