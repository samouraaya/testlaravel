<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAdministratorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function addAdmin(RegisterAdministratorRequest $request)
    { 
        $validatedData = $request->validated();

        // Create the administrator with the validated data
        $administrator = Admin::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
    
        return response()->json(['success' => 'Administrator created successfully.'], 201);
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
        $admin = Admin::where('email', $credentials['email'])->first();

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
