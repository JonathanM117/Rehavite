<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->admin) {
            return redirect()->route('admin.users.profile');
        }

        return view('admin.users.index');
    }

    public function profile()
    {
        return view('admin.users.profile');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'ends_with:@rehavite.com'],
            'password' => ['required', 'string', 'min:8'],
            'admin' => ['nullable', 'boolean'],
        ], [
            'email.ends_with' => 'El correo debe pertenecer al dominio @rehavite.com',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'admin' => $request->boolean('admin'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario registrado con éxito');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', "unique:users,email,{$user->id}", 'ends_with:@rehavite.com'],
            'password' => ['nullable', 'string', 'min:8'],
            'admin' => ['nullable', 'boolean'],
        ], [
            'email.ends_with' => 'El correo debe pertenecer al dominio @rehavite.com',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Only allow changing admin if not editing self
        if (auth()->id() !== $user->id) {
            $data['admin'] = $request->has('admin');
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado con éxito');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado con éxito');
    }
}
