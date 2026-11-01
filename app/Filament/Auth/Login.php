<?php

namespace App\Filament\Auth;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;

class Login extends BaseLogin
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            $user = Filament::auth()->user();

            if (
                $user instanceof FilamentUser
                && $user->canAccessPanel(Filament::getCurrentPanel())
            ) {
                redirect()->intended(Filament::getUrl());

                return;
            }

            // Akun lain (mis. kontributor) sedang login — lepaskan sesi agar bisa ganti akun.
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            session()->flash(
                'status',
                'Sesi sebelumnya diganti. Silakan masuk dengan akun admin/redaksi.'
            );
        }

        $this->form->fill();
    }

    public function getHeading(): string
    {
        return 'Masuk Panel Redaksi';
    }

    public function getSubheading(): ?string
    {
        return 'Khusus admin, direktur, pimpinan redaktur, dan redaktur. Kontributor memakai /masuk.';
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            Action::make('home')
                ->label('← Kembali ke beranda')
                ->url(url('/'))
                ->color('gray')
                ->link(),
        ];
    }
}
