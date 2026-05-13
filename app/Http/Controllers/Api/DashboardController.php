<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// TODO (quando banco disponível): substituir métodos privados por queries Eloquent

class DashboardController extends Controller
{
    /**
     * Dados completos do dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user'        => $user,
            'stats'       => $this->getStats($user),
            'nextSteps'   => $this->getNextSteps($user),
            'activities'  => $this->getActivities($user),
        ]);
    }

    /**
     * Estatísticas do usuário
     */
    public function stats(Request $request): JsonResponse
    {
        return response()->json([
            'stats' => $this->getStats($request->user()),
        ]);
    }

    /**
     * Atividades recentes
     */
    public function activities(Request $request): JsonResponse
    {
        return response()->json([
            'activities' => $this->getActivities($request->user()),
        ]);
    }

    /**
     * Próximos passos recomendados
     */
    public function nextSteps(Request $request): JsonResponse
    {
        return response()->json([
            'nextSteps' => $this->getNextSteps($request->user()),
        ]);
    }

    private function getStats($user): array
    {
        return [
            [
                'label'   => 'Módulos Concluídos',
                'value'   => '12',
                'total'   => '20',
                'key'     => 'modules_completed',
            ],
            [
                'label' => 'Horas de Estudo',
                'value' => '48h',
                'trend' => '+12h esta semana',
                'key'   => 'study_hours',
            ],
            [
                'label' => 'Sessões de Mentoria',
                'value' => '8',
                'total' => '12',
                'key'   => 'mentoring_sessions',
            ],
            [
                'label' => 'Crescimento',
                'value' => '85%',
                'trend' => '+5% este mês',
                'key'   => 'growth',
            ],
        ];
    }

    private function getNextSteps($user): array
    {
        return [
            [
                'title'    => 'Análise SWOT do seu negócio',
                'module'   => 'Planejamento Estratégico',
                'progress' => 75,
                'duration' => '45 min',
            ],
            [
                'title'    => 'Definição de Público-Alvo',
                'module'   => 'Marketing e Vendas',
                'progress' => 0,
                'duration' => '30 min',
            ],
            [
                'title'    => 'Estrutura de Custos',
                'module'   => 'Gestão Financeira',
                'progress' => 0,
                'duration' => '60 min',
            ],
        ];
    }

    private function getActivities($user): array
    {
        return [
            [
                'title' => 'Módulo "Planejamento Estratégico" concluído',
                'time'  => 'Há 2 horas',
                'type'  => 'success',
            ],
            [
                'title' => 'Sessão de mentoria agendada para amanhã às 15h',
                'time'  => 'Há 5 horas',
                'type'  => 'info',
            ],
            [
                'title' => 'Novo material disponível: "Análise de Mercado"',
                'time'  => 'Há 1 dia',
                'type'  => 'new',
            ],
            [
                'title' => 'Comentário do mentor no seu projeto',
                'time'  => 'Há 2 dias',
                'type'  => 'comment',
            ],
        ];
    }
}
