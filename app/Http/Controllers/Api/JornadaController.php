<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrilhaAprendizagem;
use App\Models\TrilhaUsuario;
use App\Models\ConteudoTrilha;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JornadaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'progress' => $this->getProgressOverview($request->user()),
            'modules'  => $this->getModules($request->user()),
        ]);
    }

    public function progress(Request $request): JsonResponse
    {
        return response()->json(['progress' => $this->getProgressOverview($request->user())]);
    }

    public function modules(Request $request): JsonResponse
    {
        return response()->json(['modules' => $this->getModules($request->user())]);
    }

    public function module(Request $request, int $id): JsonResponse
    {
        $modules = $this->getModules($request->user());
        $module  = collect($modules)->firstWhere('id', $id);

        if (!$module) {
            return response()->json(['message' => 'Módulo não encontrado.'], 404);
        }

        return response()->json(['module' => $module]);
    }

    public function completeLesson(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate(['lesson_id' => 'required|integer']);

        $user = $request->user();
        if ($user) {
            $conteudo = ConteudoTrilha::where('trilha_id', $id)->where('id', $validated['lesson_id'])->first();
            if ($conteudo) {
                $tu = TrilhaUsuario::firstOrCreate(
                    ['usuario_id' => $user->id, 'trilha_id' => $id],
                    ['progresso' => 0, 'status' => 'em_andamento']
                );
                $total = ConteudoTrilha::where('trilha_id', $id)->count();
                $novoProgresso = $total > 0 ? min(100, (int) ($tu->progresso + (100 / $total))) : $tu->progresso;
                $tu->progresso = $novoProgresso;
                $tu->status    = $novoProgresso >= 100 ? 'concluido' : 'em_andamento';
                $tu->save();
            }
        }

        return response()->json([
            'message'   => 'Aula marcada como concluída!',
            'module_id' => $id,
            'lesson_id' => $validated['lesson_id'],
        ]);
    }

    private function getProgressOverview($user): array
    {
        $totalTrilhas   = TrilhaAprendizagem::count();
        $totalConteudos = ConteudoTrilha::count();

        if ($totalTrilhas > 0) {
            $trilhasUsuario = $user ? TrilhaUsuario::where('usuario_id', $user->id)->get() : collect();
            $concluidas     = $trilhasUsuario->where('status', 'concluido')->count();
            $progressoMedio = $trilhasUsuario->count() > 0 ? (int) $trilhasUsuario->avg('progresso') : 0;

            return [
                'totalProgress'    => $progressoMedio,
                'completedModules' => $concluidas,
                'totalModules'     => $totalTrilhas,
                'totalHours'       => 0,
                'completedLessons' => 0,
                'totalLessons'     => $totalConteudos,
            ];
        }

        // fallback estático
        return [
            'totalProgress'    => 45,
            'completedModules' => 1,
            'totalModules'     => 6,
            'totalHours'       => 48,
            'completedLessons' => 23,
            'totalLessons'     => 81,
        ];
    }

    private function getModules($user): array
    {
        $trilhas = TrilhaAprendizagem::withCount('conteudos')->get();

        if ($trilhas->isNotEmpty()) {
            $trilhasUsuario = $user
                ? TrilhaUsuario::where('usuario_id', $user->id)->get()->keyBy('trilha_id')
                : collect();

            return $trilhas->map(function ($trilha) use ($trilhasUsuario) {
                $tu        = $trilhasUsuario->get($trilha->id);
                $progresso = $tu?->progresso ?? 0;
                $status    = $tu ? ($tu->status === 'concluido' ? 'completed' : 'in-progress') : 'locked';
                $total     = $trilha->conteudos_count;

                return [
                    'id'               => $trilha->id,
                    'title'            => $trilha->titulo,
                    'description'      => $trilha->descricao,
                    'status'           => $status,
                    'progress'         => $progresso,
                    'lessons'          => $total,
                    'completedLessons' => $total > 0 ? (int) round($total * $progresso / 100) : 0,
                    'duration'         => $trilha->estagio_alvo ?? '—',
                    'nivel_dificuldade'=> $trilha->nivel_dificuldade,
                ];
            })->values()->toArray();
        }

        // fallback estático
        return [
            ['id' => 1, 'title' => 'Planejamento Estratégico', 'description' => 'Intenção estratégica: missão, visão, valores, objetivos e públicos',          'status' => 'completed',   'progress' => 100, 'lessons' => 12, 'completedLessons' => 12, 'duration' => '4 semanas'],
            ['id' => 2, 'title' => 'Análise de Mercado',       'description' => 'Pesquisa e análise do mercado, concorrência e oportunidades',                  'status' => 'in-progress', 'progress' => 65,  'lessons' => 10, 'completedLessons' => 7,  'duration' => '3 semanas'],
            ['id' => 3, 'title' => 'Modelo de Negócio',        'description' => 'Canvas, proposta de valor e estruturação do modelo de negócio',                 'status' => 'in-progress', 'progress' => 30,  'lessons' => 15, 'completedLessons' => 4,  'duration' => '5 semanas'],
            ['id' => 4, 'title' => 'Marketing e Vendas',       'description' => 'Estratégias de marketing digital e técnicas de vendas',                         'status' => 'locked',      'progress' => 0,   'lessons' => 18, 'completedLessons' => 0,  'duration' => '6 semanas'],
            ['id' => 5, 'title' => 'Gestão Financeira',        'description' => 'Planejamento financeiro, fluxo de caixa e análise de investimentos',            'status' => 'locked',      'progress' => 0,   'lessons' => 14, 'completedLessons' => 0,  'duration' => '5 semanas'],
            ['id' => 6, 'title' => 'Operações e Processos',    'description' => 'Gestão de operações, processos e qualidade',                                    'status' => 'locked',      'progress' => 0,   'lessons' => 12, 'completedLessons' => 0,  'duration' => '4 semanas'],
        ];
    }
}
