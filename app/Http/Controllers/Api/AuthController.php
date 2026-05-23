<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Perfil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function formatUser(User $user): array
    {
        $perfil = $user->perfil;
        return [
            'id'            => $user->id,
            'name'          => $user->nome,
            'email'         => $user->email,
            'tipo_usuario'  => $user->tipo_usuario,
            'avatar'        => null,
            'bio'           => $perfil?->bio,
            'phone'         => $perfil?->telefone,
            'business'      => null,
            'city'          => null,
            'state'         => null,
        ];
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:150',
            'email'        => 'required|email|unique:usuarios,email',
            'password'     => 'required|string|min:6',
            'tipo_usuario' => 'sometimes|string|max:50',
        ]);

        $user = User::create([
            'nome'         => $validated['name'],
            'email'        => $validated['email'],
            'senha'        => Hash::make($validated['password']),
            'tipo_usuario' => $validated['tipo_usuario'] ?? 'empreendedor',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Cadastro realizado com sucesso!',
            'user'    => $this->formatUser($user),
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->senha)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user'    => $this->formatUser($user),
            'token'   => $token,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        return response()->json([
            'user' => $this->formatUser($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logout realizado com sucesso!',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:150',
            'email' => 'sometimes|email|unique:usuarios,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'bio'   => 'sometimes|string',
        ]);

        if (isset($validated['name'])) {
            $user->nome = $validated['name'];
            $user->save();
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
            $user->save();
        }

        $perfil = $user->perfil ?? new Perfil(['usuario_id' => $user->id]);
        if (isset($validated['phone'])) {
            $perfil->telefone = $validated['phone'];
        }
        if (isset($validated['bio'])) {
            $perfil->bio = $validated['bio'];
        }
        $perfil->save();

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user'    => $this->formatUser($user->fresh()),
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Se o e-mail existir, você receberá as instruções em breve.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Senha redefinida com sucesso!',
        ]);
    }
}
