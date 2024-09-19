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
        $profile = Profile::create($request->validated());

        return response()->json($profile, 201);
    }
}
