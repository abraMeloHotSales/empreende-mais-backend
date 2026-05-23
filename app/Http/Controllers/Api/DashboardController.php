<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrilhaUsuario;
use App\Models\SessaoMentoria;
use App\Models\PostForum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user'       => $user ? [
                'id'    => $user->id,
                'name'  => $user->nome,
                'email' => $user->email,
            ] : null,
            'stats'      => $this->getStats($user),
            'nextSteps'  => $this->getNextSteps($user),
            'activities' => $this->getActivities($user),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        return response()->json(['stats' => $this->getStats($request->user())]);
    }

    public function activities(Request $request): JsonResponse
    {
        return response()->json(['activities' => $this->getActivities($request->user())]);
    }

    public function nextSteps(Request $request): JsonResponse
    {
        return response()->json(['nextSteps' => $this->getNextSteps($request->user())]);
    }

    private function getStats($user): array
    {
        if ($user) {
            $trilhasUsuario    = TrilhaUsuario::where('usuario_id', $user->id)->get();
            $totalTrilhas      = $trilhasUsuario->count();
            $concluidasTrilhas = $trilhasUsuario->where('status', 'concluido')->count();
            $totalSessoes      = SessaoMentoria::where('aluno_id', $user->id)->count();
            $realizadas        = SessaoMentoria::where('aluno_id', $user->id)->where('status', 'realizado')->count();
            $progressoMedio    = $totalTrilhas > 0 ? (int) $trilhasUsuario->avg('progresso') : 0;

            if ($totalTrilhas > 0 || $totalSessoes > 0) {
                return [
                    ['label' => 'Trilhas Concluídas',   'value' => (string) $concluidasTrilhas, 'total' => (string) $totalTrilhas, 'key' => 'modules_completed'],
                    ['label' => 'Horas de Estudo',       'value' => '—',   'trend' => null,               'key' => 'study_hours'],
                    ['label' => 'Sessões de Mentoria',   'value' => (string) $realizadas,        'total' => (string) $totalSessoes, 'key' => 'mentoring_sessions'],
                    ['label' => 'Progresso Médio',       'value' => $progressoMedio . '%',       'trend' => null,                   'key' => 'growth'],
                ];
            }
        }

        // fallback estático
        return [
            ['label' => 'Módulos Concluídos',  'value' => '12', 'total' => '20', 'key' => 'modules_completed'],
            ['label' => 'Horas de Estudo',      'value' => '48h', 'trend' => '+12h esta semana', 'key' => 'study_hours'],
            ['label' => 'Sessões de Mentoria',  'value' => '8',  'total' => '12', 'key' => 'mentoring_sessions'],
            ['label' => 'Crescimento',          'value' => '85%', 'trend' => '+5% este mês',    'key' => 'growth'],
        ];
    }

    private function getNextSteps($user): array
    {
        if ($user) {
            $emAndamento = TrilhaUsuario::with('trilha')
                ->where('usuario_id', $user->id)
                ->where('status', '!=', 'concluido')
                ->orderBy('progresso', 'desc')
                ->take(3)
                ->get();

            if ($emAndamento->isNotEmpty()) {
                return $emAndamento->map(fn($tu) => [
                    'title'    => $tu->trilha?->titulo ?? 'Trilha',
                    'module'   => $tu->trilha?->nivel_dificuldade ?? '',
                    'progress' => $tu->progresso ?? 0,
                    'duration' => '—',
                ])->values()->toArray();
            }
        }

        // fallback estático
        return [
            ['title' => 'Análise SWOT do seu negócio',  'module' => 'Planejamento Estratégico', 'progress' => 75, 'duration' => '45 min'],
            ['title' => 'Definição de Público-Alvo',    'module' => 'Marketing e Vendas',        'progress' => 0,  'duration' => '30 min'],
            ['title' => 'Estrutura de Custos',          'module' => 'Gestão Financeira',         'progress' => 0,  'duration' => '60 min'],
        ];
    }

    private function getActivities($user): array
    {
        if ($user) {
            $sessoes = SessaoMentoria::where('aluno_id', $user->id)->orderByDesc('agendado_em')->take(3)->get();
            $posts   = PostForum::where('usuario_id', $user->id)->latest('criado_em')->take(3)->get();

            $activities = [];
            foreach ($sessoes as $s) {
                $activities[] = [
                    'title' => 'Sessão de mentoria: ' . ($s->observacoes ?? $s->status),
                    'time'  => $s->agendado_em?->diffForHumans() ?? '—',
                    'type'  => $s->status === 'realizado' ? 'success' : 'info',
                ];
            }
            foreach ($posts as $p) {
                $activities[] = [
                    'title' => 'Post no fórum: ' . $p->titulo,
                    'time'  => $p->criado_em?->diffForHumans() ?? '—',
                    'type'  => 'comment',
                ];
            }

            if (!empty($activities)) {
                return array_slice($activities, 0, 5);
            }
        }

        // fallback estático
        return [
            ['title' => 'Módulo "Planejamento Estratégico" concluído',      'time' => 'Há 2 horas', 'type' => 'success'],
            ['title' => 'Sessão de mentoria agendada para amanhã às 15h',   'time' => 'Há 5 horas', 'type' => 'info'],
            ['title' => 'Novo material disponível: "Análise de Mercado"',   'time' => 'Há 1 dia',   'type' => 'new'],
            ['title' => 'Comentário do mentor no seu projeto',               'time' => 'Há 2 dias',  'type' => 'comment'],
        ];
    }
}
