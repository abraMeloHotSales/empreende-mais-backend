<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// TODO (quando banco disponível): substituir arrays estáticos por Certificate::where('user_id', ...)->get()

class CertificadosController extends Controller
{
    /**
     * Dados completos de certificados e conquistas
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'certificates' => $this->getCertificates($request->user()),
            'achievements' => $this->getAchievements($request->user()),
            'stats'        => $this->getStats($request->user()),
        ]);
    }

    /**
     * Lista de certificados do usuário
     */
    public function certificates(Request $request): JsonResponse
    {
        return response()->json([
            'certificates' => $this->getCertificates($request->user()),
        ]);
    }

    /**
     * Download de um certificado (retorna URL do PDF)
     */
    public function download(Request $request, int $id): JsonResponse
    {
        $certificates = $this->getCertificates($request->user());
        $cert         = collect($certificates)->firstWhere('id', $id);

        if (!$cert || $cert['status'] !== 'completed') {
            return response()->json([
                'message' => 'Certificado não disponível para download.',
            ], 404);
        }

        return response()->json([
            'message'     => 'Download disponível!',
            'download_url' => url("/api/certificados/{$id}/pdf"),
        ]);
    }

    /**
     * Conquistas do usuário
     */
    public function achievements(Request $request): JsonResponse
    {
        return response()->json([
            'achievements' => $this->getAchievements($request->user()),
        ]);
    }

    /**
     * Estatísticas de certificados
     */
    public function stats(Request $request): JsonResponse
    {
        return response()->json([
            'stats' => $this->getStats($request->user()),
        ]);
    }

    private function getCertificates($user): array
    {
        return [
            [
                'id'        => 1,
                'title'     => 'Planejamento Estratégico',
                'module'    => 'Módulo Completo',
                'issueDate' => '2026-04-28',
                'hours'     => 40,
                'status'    => 'completed',
                'progress'  => 100,
            ],
            [
                'id'        => 2,
                'title'     => 'Análise de Mercado',
                'module'    => 'Em andamento',
                'issueDate' => null,
                'hours'     => 30,
                'status'    => 'in-progress',
                'progress'  => 65,
            ],
            [
                'id'        => 3,
                'title'     => 'Modelo de Negócio',
                'module'    => 'Em andamento',
                'issueDate' => null,
                'hours'     => 45,
                'status'    => 'in-progress',
                'progress'  => 30,
            ],
        ];
    }

    private function getAchievements($user): array
    {
        return [
            [
                'icon'        => '🎯',
                'title'       => 'Primeiro Módulo',
                'description' => 'Complete seu primeiro módulo',
                'earned'      => true,
                'date'        => '2026-04-28',
            ],
            [
                'icon'        => '🔥',
                'title'       => 'Sequência de 7 Dias',
                'description' => 'Estude por 7 dias consecutivos',
                'earned'      => true,
                'date'        => '2026-04-25',
            ],
            [
                'icon'        => '⭐',
                'title'       => 'Mentor Ativo',
                'description' => 'Participe de 5 sessões de mentoria',
                'earned'      => true,
                'date'        => '2026-04-21',
            ],
            [
                'icon'        => '💬',
                'title'       => 'Participante Ativo',
                'description' => 'Faça 10 contribuições na comunidade',
                'earned'      => false,
                'progress'    => 7,
            ],
            [
                'icon'        => '🎓',
                'title'       => 'Mestre Empreendedor',
                'description' => 'Complete todos os módulos',
                'earned'      => false,
                'progress'    => 17,
            ],
            [
                'icon'        => '🚀',
                'title'       => 'Aprendiz Dedicado',
                'description' => 'Acumule 100 horas de estudo',
                'earned'      => false,
                'progress'    => 48,
            ],
        ];
    }

    private function getStats($user): array
    {
        return [
            'certificatesEarned'     => 1,
            'achievementsUnlocked'   => 3,
            'totalStudyHours'        => 48,
        ];
    }
}
