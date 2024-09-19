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
        $profile = Profile::create([
            'name' => $request->name,
            'administrator_id' => Auth::id(),
            'first_name' => $request->first_name,
            'image' => $request->file('image')->store('images'),
            'status' => $request->status,
        ]);

        return response()->json($profile, 201);
    }
}
