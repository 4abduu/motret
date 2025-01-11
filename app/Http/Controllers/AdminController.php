<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function manageUsers()
    {
        $users = User::all();
        return view('admin.manageUsers', compact('users'));
    }

    public function managePhotos()
    {
        $photos = Photo::all();
        return view('admin.managePhotos', compact('photos'));
    }

    public function createUser(Request $request)
{
    $messages = [
        'name.required' => 'Nama harus diisi.',
        'username.required' => 'Username harus diisi.',
        'username.unique' => 'Username sudah digunakan.',
        'username.min' => 'Username minimal harus 4 karakter.',
        'username.max' => 'Username maksimal 20 karakter.',
        'email.required' => 'Email harus diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'password.required' => 'Password harus diisi.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'password.min' => 'Password minimal harus 8 karakter.',
    ];
 
    $validated = $request->validate([
        'name' => 'required',
        'username' => 'required|unique:users,username|min:4|max:20',
        'email' => 'required|email|unique:users,email',
        'password' => ['required', 'confirmed', Password::min(8)],
        'role' => 'required|in:admin,user',
        'subscription_ends_at' => 'nullable|date',
    ], $messages);

    User::create([
        'name' => $validated['name'],
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
        'subscription_ends_at' => $validated['subscription_ends_at'],
    ]);

    return redirect()->route('admin.users');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.editUser', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|min:4|max:20|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()],
            'role' => 'required|in:admin,user',
        ]);

        $user = User::findOrFail($id);
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('admin.users');
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.users');
    }

    public function editPhoto($id, Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'path' => 'required',
            'hashtags' => 'nullable',
            'likes' => 'nullable|integer',
            'status' => 'required',
        ]);

        $photo = Photo::findOrFail($id);
        $photo->update($validated);
        return redirect()->route('admin.photos');
    }

    public function deletePhoto($id)
    {
        Photo::findOrFail($id)->delete();
        return redirect()->route('admin.photos');
    }
}