<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens;

    
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
            'phone' => 'nullable|string|unique:users',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email']?? null,
            'phone' => $validatedData['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Registration successful','user' => $user,'token' => $token,], 201);
    }

  
    public function login(Request $request)
{
    $credentials = $request->only(['login', 'password']);

    $user = User::where('email', $credentials['login'])
        ->orWhere('phone', $credentials['login'])
        ->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // Handle MFA (if required)
    if ($user->requires_mfa) {
        $mfaToken = $this->sendMfaToken($user);
        return response()->json([
            'message' => 'MFA required. Token sent to your registered email or phone.',
            'mfa_token' => $mfaToken,
        ], 200);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['message' => 'Login successful',
        'user' => $user,
        'token' => $token,
    ], 200);
}


  
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }


     /**
     * Verify MFA token.
     */
    public function verifyMfa(Request $request)
    {
        $validated = $request->validate([
            'mfa_token' => 'required|string',
        ]);

        $user = User::where('mfa_token', $validated['mfa_token'])->first();

        if (!$user || !$user->mfa_token_valid_until || now()->isAfter($user->mfa_token_valid_until)) {
            return response()->json(['error' => 'Invalid or expired MFA token'], 401);
        }

        $user->update(['mfa_token' => null, 'mfa_token_valid_until' => null]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'MFA verification successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Send MFA token (for demo purposes, generates and returns a token).
     */
    private function sendMfaToken(User $user)
    {
        $mfaToken = strtoupper(uniqid());

        $user->update([
            'mfa_token' => $mfaToken,
            'mfa_token_valid_until' => now()->addMinutes(5),
        ]);

        // Simulate sending the token (via email, SMS, etc.)
        \Log::info("MFA token for user {$user->id}: {$mfaToken}");

        return $mfaToken;
    }
}
