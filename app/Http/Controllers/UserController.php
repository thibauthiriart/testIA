<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::all();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user->syncRoles([$request->role]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Rôle mis à jour avec succès.',
                'user' => $user->load('roles')
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}