<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List all users.
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Insert a new user in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required|min:6',
            'balance' => 'required|numeric|min:0',
            'user_type' => ['required', Rule::in(['A', 'C'])],
            'email' => 'required|email|unique:users',
        ]);

        // Generate a unique account number (10 digits, not starting with 0)
        $accountNumber = $this->__generateUniqueAccountNumber();

        // Create the user in the database
        User::create([
            'name' => $request->input('name'),
            'password' => bcrypt($request->input('password')),
            'balance' => $request->input('balance'),
            'user_type' => $request->input('user_type'),
            'account_number' => $accountNumber,
            'email' => $request->input('email'),
        ]);

        return response()->json(['success' => true, 'message' => 'User created.']);
    }

    private function __generateUniqueAccountNumber()
    {
        // Generate a unique account number (10 digits, not starting with 0)
        do {
            $accountNumber = mt_rand(1000000000, 9999999999);
        } while (User::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }

    /**
     * Show datails from an specific user.
     */
    public function show(User $user)
    {
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'nullable|min:6',
            'balance' => 'required|numeric|min:0',
            'user_type' => ['required', Rule::in(['A', 'C'])],
            'email' => 'required|email|unique:users',
        ]);
    
        $user->update([
            'name' => $request->input('name'),
            'password' => bcrypt($request->input('password')),
            'balance' => $request->input('balance'),
            'user_type' => $request->input('user_type'),
            'email' => $request->input('email'),
        ]);
    
        return response()->json(['success' => true, 'message' => 'User updated.']);
    }

    /**
     * Remove user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted.']);
    }
}
