<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filtrage par recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par rôle
        if ($request->has('is_admin') && $request->is_admin != '') {
            $query->where('is_admin', $request->is_admin === 'true');
        }
        
        $users = $query->withCount('orders')->paginate(10);
        
        return response()->json($users);
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean',
            'email_verified' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin ?? false,
            'email_verified' => $request->email_verified ?? true,
        ]);
        
        return response()->json($user, 201);
    }
    
    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['orders.items.product']);
        return response()->json($user);
    }
    
    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'is_admin' => 'boolean',
            'email_verified' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $userData = $request->only(['name', 'email', 'is_admin', 'email_verified']);
        
        if ($request->has('password') && $request->password) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $user->update($userData);
        
        return response()->json($user);
    }
    
    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Ne pas supprimer le compte admin principal
        if ($user->email === 'admin@dressingdespiplettes.com') {
            return response()->json(['message' => 'Impossible de supprimer le compte administrateur principal.'], 403);
        }
        
        $user->delete();
        
        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}