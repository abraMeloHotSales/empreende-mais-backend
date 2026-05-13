<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/*
 * TODO (quando banco estiver configurado):
 *  - Reativar User::create(), Auth::attempt(), $user->createToken()
 *  - Reativar validações únicas (unique:users)
 *  - Reativar $request->user() nas rotas protegidas
 */
class AuthController extends Controller
{
    private function staticUser(): array
    {
        return [
            'id'       => 1,
            'name'     => 'Empreendedor Demo',
            'email'    => 'demo@empreende-mais.com.br',
            'avatar'   => null,
            'bio'      => 'Usuário de demonstração da plataforma Empreende+',
            'business' => 'Meu Negócio LTDA',
            'city'     => 'Belo Horizonte',
            'state'    => 'MG',
        ];
    }

    public function register(Request $request): JsonResponse
    {
        // TODO: User::create([...]) quando banco disponível
        return response()->json([
            'message' => 'Cadastro realizado com sucesso!',
            'user'    => $this->staticUser(),
            'token'   => 'static-token-demo',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        // TODO: Auth::attempt() quando banco disponível
        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user'    => $this->staticUser(),
            'token'   => 'static-token-demo',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        // TODO: return $request->user() quando banco disponível
        return response()->json([
            'user' => $this->staticUser(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // TODO: $request->user()->currentAccessToken()->delete() quando banco disponível
        return response()->json([
            'message' => 'Logout realizado com sucesso!',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        // TODO: $request->user()->update([...]) quando banco disponível
        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user'    => array_merge($this->staticUser(), $request->only([
                'name', 'email', 'phone', 'bio', 'business', 'city', 'state',
            ])),
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
