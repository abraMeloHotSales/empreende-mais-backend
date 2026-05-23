<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perfil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ConfiguracoesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'profile'       => $this->getProfile($user),
            'notifications' => $this->defaultNotifications(),
            'preferences'   => $this->defaultPreferences(),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:150',
            'email'    => 'sometimes|email|unique:usuarios,email,' . $user->id,
            'phone'    => 'sometimes|string|max:20',
            'bio'      => 'sometimes|string',
            'business' => 'sometimes|string|max:150',
            'city'     => 'sometimes|string|max:100',
            'state'    => 'sometimes|string|max:100',
        ]);

        if (!empty($validated['name']))  $user->nome  = $validated['name'];
        if (!empty($validated['email'])) $user->email = $validated['email'];
        $user->save();

        $perfil = $user->perfil ?? new Perfil(['usuario_id' => $user->id]);
        if (isset($validated['phone'])) $perfil->telefone = $validated['phone'];
        if (isset($validated['bio']))   $perfil->bio      = $validated['bio'];
        $perfil->save();

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'profile' => $this->getProfile($user->fresh()),
        ]);
    }

    public function updateNotifications(Request $request): JsonResponse
    {
        return response()->json([
            'message'       => 'Notificações atualizadas com sucesso!',
            'notifications' => array_merge(
                $this->defaultNotifications(),
                array_filter($request->only(['emailSessions', 'emailModules', 'emailCommunity', 'pushEnabled']), fn($v) => $v !== null),
            ),
        ]);
    }

    public function updateSecurity(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->senha)) {
            throw ValidationException::withMessages(['current_password' => ['Senha atual incorreta.']]);
        }

        $user->senha = Hash::make($validated['new_password']);
        $user->save();

        return response()->json(['message' => 'Senha atualizada com sucesso!']);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        return response()->json([
            'message'     => 'Preferências atualizadas com sucesso!',
            'preferences' => array_merge(
                $this->defaultPreferences(),
                array_filter($request->only(['theme', 'language'])),
            ),
        ]);
    }

    private function getProfile($user): array
    {
        if ($user) {
            $perfil = $user->perfil;
            return [
                'name'     => $user->nome,
                'email'    => $user->email,
                'phone'    => $perfil?->telefone,
                'bio'      => $perfil?->bio,
                'business' => null,
                'city'     => null,
                'state'    => null,
                'avatar'   => null,
            ];
        }

        // fallback estático
        return [
            'name'     => 'Empreendedor Demo',
            'email'    => 'demo@empreende-mais.com.br',
            'phone'    => '(31) 99999-0000',
            'bio'      => 'Apaixonado por empreendedorismo e inovação.',
            'business' => 'Meu Negócio LTDA',
            'city'     => 'Belo Horizonte',
            'state'    => 'MG',
            'avatar'   => null,
        ];
    }

    private function defaultNotifications(): array
    {
        return [
            'emailSessions'  => true,
            'emailModules'   => true,
            'emailCommunity' => false,
            'pushEnabled'    => true,
        ];
    }

    private function defaultPreferences(): array
    {
        return ['theme' => 'system', 'language' => 'pt_BR'];
    }
}
