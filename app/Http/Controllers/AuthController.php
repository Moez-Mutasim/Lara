<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Authentication", description="Endpoints related to user authentication")
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    use HasApiTokens;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['register', 'login']);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Registration successful"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|required_without:phone|email|unique:users',
            'phone' => 'nullable|required_without:email|string|unique:users',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            Log::warning('User registration failed due to validation errors', ['errors' => $validator->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('New user registered', ['user_id' => $user->user_id]);

        return response()->json(['message' => 'Registration successful', 'user' => $user, 'token' => $token], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login", "password"},
     *             @OA\Property(property="login", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('phone', $request->login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning('Invalid login attempt', ['login' => $request->login]);
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('User logged in', ['user_id' => $user->user_id]);

        return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the user",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}}
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        Log::info('User logged out', ['user_id' => $request->user()->user_id]);

        return response()->json(['message' => 'Successfully logged out']);
    }
}
