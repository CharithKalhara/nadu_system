<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesktopUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->requireAdmin($request);

        return response()->json(['data' => User::query()->orderBy('name')->get(['id', 'name', 'email', 'role', 'created_at'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->requireAdmin($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'typing'])],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $user = User::query()->create($data);

        return response()->json(['user' => $user->only(['id', 'name', 'email', 'role', 'created_at'])], 201);
    }

    public function update(Request $request, string $user): JsonResponse
    {
        $this->requireAdmin($request);
        $record = User::query()->findOrFail($user);
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($record->id)],
            'role' => ['sometimes', 'required', Rule::in(['admin', 'typing'])],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
        ]);

        if (($data['role'] ?? null) !== 'admin' && $record->role === 'admin') {
            abort_if(User::query()->where('role', 'admin')->count() < 2, 422, 'The last administrator must remain an administrator.');
        }

        if (($data['password'] ?? null) === null) {
            unset($data['password']);
        }

        $record->fill($data)->save();

        return response()->json(['user' => $record->only(['id', 'name', 'email', 'role', 'created_at'])]);
    }

    public function destroy(Request $request, string $user): JsonResponse
    {
        $this->requireAdmin($request);
        $record = User::query()->findOrFail($user);

        abort_if($record->is($request->user()), 422, 'You cannot delete the account currently in use.');
        abort_if($record->role === 'admin' && User::query()->where('role', 'admin')->count() < 2, 422, 'The last administrator cannot be deleted.');

        $record->delete();

        return response()->json(['message' => 'User deleted.']);
    }

    private function requireAdmin(Request $request): void
    {
        abort_unless($request->user()->role === 'admin', 403);
    }
}
