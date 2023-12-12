<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        throw ValidationException::withMessages($validator->errors()->toArray());
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;
        $user->api_token = $token;
        $user->save();

        return response()->json([
            'message' => __('notifications.logged_in'),
            'user' => $user,
            '_token' => 'Bearer ' . $token,
        ], JsonResponse::HTTP_OK);
    } else {
        return response()->json(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}

    public function profile(Request $request)
    {
        $user = Auth::user();

        return response()->json(['user' => $user], 200);
    }
}
