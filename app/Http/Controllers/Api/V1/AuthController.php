<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            Log::notice('mobile_api.auth.failed', ['ip' => $request->ip()]);

            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        if ($user->role !== 'typing') {
            Log::notice('mobile_api.auth.role_denied', [
                'user_id' => $user->getKey(),
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'This account is not authorised for mobile entry.'], 403);
        }

        $token = $user->createToken(
            $credentials['device_name'] ?: 'Nadu Mobile',
            ['mobile:use'],
            now()->addMinutes((int) config('sanctum.expiration', 10080)),
        );

        Log::info('mobile_api.auth.login', ['user_id' => $user->getKey()]);

        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
                'user' => [
                    'id' => $user->getKey(),
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $userId = $request->user()->getKey();
        $request->user()->currentAccessToken()?->delete();

        Log::info('mobile_api.auth.logout', ['user_id' => $userId]);

        return response()->json(['data' => ['message' => 'Signed out.']]);
    }
}
