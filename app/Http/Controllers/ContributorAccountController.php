<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ContributorAccountController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user?->isKontributor()) {
            return redirect()->route('news.index');
        }

        return view('account.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user?->isKontributor()) {
            return redirect()->route('news.index');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'current_password.required_with' => 'Isi password saat ini untuk ganti password.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->name = $validated['name'];

        if (filled($validated['password'] ?? null)) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('status', 'Profil berhasil diperbarui.');
    }
}
