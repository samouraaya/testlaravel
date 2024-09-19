<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAdministratorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function register(RegisterAdministratorRequest $request)
    {
        $validatedData = $request->validated();

        // Créez l'administrateur avec les données validées
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
        // Valider les données d'entrée
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Authentifier l'utilisateur
        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
        
            // Récupérer l'administrateur authentifié
            $admin = Auth::guard('admin')->user();
            if ($admin instanceof Admin) {
                // Générer un token
                $token = $admin->createToken('AdminToken')->plainTextToken;

                // Retourner la réponse avec le token
                return response()->json([
                    'token' => $token,
                    'message' => 'Login successful'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid user model'
                ], 500);
            }
        }

        // Retourner une réponse d'erreur en cas d'échec
        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }

}
