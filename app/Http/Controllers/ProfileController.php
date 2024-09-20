<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:administrator');
    }

    public function store(ProfileRequest $request)
    {
       
        $validatedData =(object)$request->validated();
    
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/profiles', 'public');
        } else {
            return response()->json(['error' => 'Image file is required.'], 400);
        }
        
        $profile = Profile::create([
            'last_name' => $validatedData->last_name,
            'first_name' => $validatedData->first_name,
            'image' => $path,
            'status' => $validatedData->status,
            'administrator_id' => auth()->user()->id, 
        ]);

        return response()->json($profile, 201);
        
    }
}
