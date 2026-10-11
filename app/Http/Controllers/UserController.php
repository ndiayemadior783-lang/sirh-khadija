<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('utilisateurs.index', compact('users'));
    }

    public function create()
    {
        return view('utilisateurs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'role' => ['required', 'in:admin,rh,manager,employe'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        activity()->causedBy($request->user())->withProperties(['email' => $data['email'], 'role' => $data['role']])->log('Utilisateur cree');

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur ajoute avec succes.');
    }

    public function edit(User $user)
    {
        return view('utilisateurs.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,rh,manager,employe'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        activity()->performedOn($user)->causedBy($request->user())->log('Utilisateur modifie');

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur mis a jour.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6'],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        activity()->performedOn($user)->causedBy($request->user())->log('Mot de passe reinitialise');

        return back()->with('success', 'Mot de passe change pour ' . $user->name . '.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        activity()->performedOn($user)->causedBy($request->user())->log('Utilisateur supprime');
        $user->delete();

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur supprime.');
    }
}