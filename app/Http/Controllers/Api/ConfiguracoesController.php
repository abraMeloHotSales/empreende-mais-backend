<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// TODO (quando banco disponível):
//  - updateProfile: $request->user()->update([...])
//  - updateSecurity: Hash::check() + $request->user()->update(['password' => Hash::make(...)])

class ConfiguracoesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'profile'       => $this->staticProfile(),
            'notifications' => $this->staticNotifications(),
            'preferences'   => $this->staticPreferences(),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        // TODO: $request->user()->update($validated) quando banco disponível
        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'profile' => array_merge($this->staticProfile(), array_filter($request->only([
                'name', 'email', 'phone', 'bio', 'business', 'city', 'state',
            ]))),
        ]);
    }

    public function updateNotifications(Request $request): JsonResponse
    {
        // TODO: salvar no banco quando disponível
        return response()->json([
            'message'       => 'Notificações atualizadas com sucesso!',
            'notifications' => array_merge($this->staticNotifications(), array_filter(
                $request->only(['emailSessions', 'emailModules', 'emailCommunity', 'pushEnabled']),
                fn ($v) => $v !== null,
            )),
        ]);
    }

    public function updateSecurity(Request $request): JsonResponse
    {
        // TODO: Hash::check() + update quando banco disponível
        return response()->json([
            'message' => 'Senha atualizada com sucesso!',
        ]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        // TODO: salvar no banco quando disponível
        return response()->json([
            'message'     => 'Preferências atualizadas com sucesso!',
            'preferences' => array_merge($this->staticPreferences(), array_filter(
                $request->only(['theme', 'language']),
            )),
        ]);
    }

    private function staticProfile(): array
    {
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

    private function staticNotifications(): array
    {
        return [
            'emailSessions'  => true,
            'emailModules'   => true,
            'emailCommunity' => false,
            'pushEnabled'    => true,
        ];
    }

    private function staticPreferences(): array
    {
        return [
            'theme'    => 'system',
            'language' => 'pt_BR',
        ];
    }
}
