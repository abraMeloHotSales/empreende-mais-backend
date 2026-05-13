<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// TODO (quando banco disponível): substituir arrays estáticos por Module::with(...)->get()

class JornadaController extends Controller
{
    /**
     * Jornada completa do usuário
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'progress' => $this->getProgressOverview($request->user()),
            'modules'  => $this->getModules($request->user()),
        ]);
    }

    /**
     * Progresso geral da jornada
     */
    public function progress(Request $request): JsonResponse
    {
        return response()->json([
            'progress' => $this->getProgressOverview($request->user()),
        ]);
    }

    /**
     * Lista de módulos
     */
    public function modules(Request $request): JsonResponse
    {
        return response()->json([
            'modules' => $this->getModules($request->user()),
        ]);
    }

    /**
     * Detalhes de um módulo específico
     */
    public function module(Request $request, int $id): JsonResponse
    {
        $modules = $this->getModules($request->user());
        $module  = collect($modules)->firstWhere('id', $id);

        if (!$module) {
            return response()->json(['message' => 'Módulo não encontrado.'], 404);
        }

        return response()->json(['module' => $module]);
    }

    /**
     * Marcar aula como concluída
     */
    public function completeLesson(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'lesson_id' => 'required|integer',
        ]);

        return response()->json([
            'message'   => 'Aula marcada como concluída!',
            'module_id' => $id,
            'lesson_id' => $validated['lesson_id'],
        ]);
    }

    private function getProgressOverview($user): array
    {
        return [
            'totalProgress'      => 45,
            'completedModules'   => 1,
            'totalModules'       => 6,
            'totalHours'         => 48,
            'completedLessons'   => 23,
            'totalLessons'       => 81,
        ];
    }

    private function getModules($user): array
    {
        return [
            [
                'id'               => 1,
                'title'            => 'Planejamento Estratégico',
                'description'      => 'Intenção estratégica: missão, visão, valores, objetivos e públicos',
                'status'           => 'completed',
                'progress'         => 100,
                'lessons'          => 12,
                'completedLessons' => 12,
                'duration'         => '4 semanas',
            ],
            [
                'id'               => 2,
                'title'            => 'Análise de Mercado',
                'description'      => 'Pesquisa e análise do mercado, concorrência e oportunidades',
                'status'           => 'in-progress',
                'progress'         => 65,
                'lessons'          => 10,
                'completedLessons' => 7,
                'duration'         => '3 semanas',
            ],
            [
                'id'               => 3,
                'title'            => 'Modelo de Negócio',
                'description'      => 'Canvas, proposta de valor e estruturação do modelo de negócio',
                'status'           => 'in-progress',
                'progress'         => 30,
                'lessons'          => 15,
                'completedLessons' => 4,
                'duration'         => '5 semanas',
            ],
            [
                'id'               => 4,
                'title'            => 'Marketing e Vendas',
                'description'      => 'Estratégias de marketing digital e técnicas de vendas',
                'status'           => 'locked',
                'progress'         => 0,
                'lessons'          => 18,
                'completedLessons' => 0,
                'duration'         => '6 semanas',
            ],
            [
                'id'               => 5,
                'title'            => 'Gestão Financeira',
                'description'      => 'Planejamento financeiro, fluxo de caixa e análise de investimentos',
                'status'           => 'locked',
                'progress'         => 0,
                'lessons'          => 14,
                'completedLessons' => 0,
                'duration'         => '5 semanas',
            ],
            [
                'id'               => 6,
                'title'            => 'Operações e Processos',
                'description'      => 'Gestão de operações, processos e qualidade',
                'status'           => 'locked',
                'progress'         => 0,
                'lessons'          => 12,
                'completedLessons' => 0,
                'duration'         => '4 semanas',
            ],
        ];
    }
}
