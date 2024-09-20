<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function addUser(RegisterUserRequest $request)
    { 
        $validatedData = $request->validated();
        // find role 
        $role = Role::where('name', $validatedData['role'])->first();

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }
  
            // Create a new user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => $role->id, // Use id role
        ]);

        return response()->json($user, 201);
        
    
    }

     /**
     * Authentifie un administrateur et retourne un token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validation des champs de requête
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Vérification manuelle des informations d'identification
        $admin = User::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Création du token avec Sanctum
        $token = $admin->createToken('AdminToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token
        ], 200);
    }

}
