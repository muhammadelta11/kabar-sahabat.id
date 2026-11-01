<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;

class AuthenticateFilament extends FilamentAuthenticate
{
    /**
     * @param  array<int, string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();
        $panel = Filament::getCurrentPanel();

        if ($user instanceof FilamentUser && (! $user->canAccessPanel($panel))) {
            $guard->logout();

            $request->session()->flash(
                'status',
                'Akun yang sedang masuk tidak punya akses panel. Silakan login dengan akun admin/redaksi.'
            );

            throw new AuthenticationException(
                'Unauthenticated.',
                $guards,
                Filament::getLoginUrl() ?: url('/admin/login'),
            );
        }
    }
}
