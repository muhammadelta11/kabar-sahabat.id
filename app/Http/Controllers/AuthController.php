<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\UserAccountStatus;
use App\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Admin/redaksi yang sudah login diarahkan ke panel, bukan form kontributor.
        if ($user?->role instanceof UserRole && $user->role->canAccessPanel()) {
            return redirect('/admin');
        }

        return view('auth.login', [
            'currentUser' => $user?->isKontributor() ? $user : null,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Lepaskan sesi lama agar bisa ganti akun (kontributor ↔ admin, dll).
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($user?->isKontributor()) {
            if (! $user->isAccountActive()) {
                return redirect()->route('auth.pending');
            }

            return redirect()->intended(route('submissions.create'));
        }

        if ($user?->role instanceof UserRole && $user->role->canAccessPanel()) {
            return redirect()->intended('/admin');
        }

        return redirect()->intended(route('news.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'agreement' => ['accepted'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar. Silakan masuk.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'agreement.accepted' => 'Anda harus menyetujui ketentuan bergabung.',
        ]);

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Kontributor,
            'account_status' => UserAccountStatus::Pending,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('auth.pending')
            ->with('status', 'Pendaftaran diterima. Akun menunggu persetujuan redaksi.');
    }

    public function showPending(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user?->isKontributor()) {
            return redirect()->route('news.index');
        }

        if ($user->canSubmitArticles()) {
            return redirect()->route('submissions.create');
        }

        return view('auth.pending', [
            'user' => $user,
            'status' => $user->account_status,
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('news.index');
    }
}
