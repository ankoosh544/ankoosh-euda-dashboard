<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        $userAttributes = $user->only([
            'id', 'name', 'email', 'password', 'isAdmin', 'isTechnician', 'api_token', 'created_at', 'updated_at'
        ]);

        return response()->json([
            'message' => __('notifications.logged_in'),
            'user' => $userAttributes,
            '_token' => 'Bearer ' . $token,
        ], JsonResponse::HTTP_OK);
    } else {
        return response()->json(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}

    public function profile(Request $request)
    {
        $user = Auth::user();
        $userAttributes = $user->only([
            'id', 'name', 'email', 'password', 'isAdmin', 'isTechnician', 'api_token', 'created_at', 'updated_at'
        ]);

        return response()->json(['user' => $userAttributes], 200);
    }

//     public function generateQrCode(Request $request)
// {
//     // Generate QR code with plantId
//     QrCode::generate($plantId, public_path('qr_codes/'.$plantId.'.png'));

//     return "QR code generated for Plant ID: $plantId";
// }

// public function qrcodeForm()
// {
//     return view('generate_qrcode');
// }
}
