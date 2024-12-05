<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OpenApi\Annotations as OA;

/**
 * @OA\Server(
 *     url="http://localhost/api",
 *     description="Local API server"
 * )
 */
class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate a user and generate a token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User login credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        // Generate a token after successful authentication
        $token = Auth::user()->createToken('Personal Access Token')->plainTextToken;

        // Return the token along with a success message
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout a user and revoke their token",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Revoke user tokens",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        // Revoke the token if using Sanctum or any other token-based system
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
