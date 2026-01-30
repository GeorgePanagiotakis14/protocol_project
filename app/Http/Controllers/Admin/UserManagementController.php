<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'is_admin' => ['nullable','boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => (bool)($validated['is_admin'] ?? false),
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Ο χρήστης δημιουργήθηκε.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'is_admin' => ['nullable','boolean'],
        ]);

        // Προστασία: μην επιτρέψεις να αφαιρέσει ο admin τον admin ρόλο από τον εαυτό του
        $isAdmin = (bool)($validated['is_admin'] ?? false);
        if ($request->user()->id === $user->id) {
            $isAdmin = true;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $isAdmin,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Ο χρήστης ενημερώθηκε.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.edit', $user)->with('success', 'Ο κωδικός άλλαξε.');
    }

    public function destroy(Request $request, User $user)
    {
        // Μην επιτρέψεις να διαγράψει τον εαυτό του
        if ($request->user()->id === $user->id) {
            return redirect()->back()->with('success', 'Δεν μπορείς να διαγράψεις τον εαυτό σου.');
        }

        // Μην επιτρέψεις διαγραφή admin χρήστη
        if ($user->is_admin) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Ο χρήστης διαγράφηκε.');
    }
}
