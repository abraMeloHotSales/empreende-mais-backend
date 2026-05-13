<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// TODO (quando banco disponível): substituir arrays estáticos por MentoringSession::where('user_id', ...)->get()

class MentoriaController extends Controller
{
    /**
     * Dados completos de mentoria
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'mentor'           => $this->getMentor(),
            'upcomingSessions' => $this->getUpcomingSessions($request->user()),
            'pastSessions'     => $this->getPastSessions($request->user()),
        ]);
    }

    /**
     * Dados do mentor do usuário
     */
    public function mentor(Request $request): JsonResponse
    {
        return response()->json(['mentor' => $this->getMentor()]);
    }

    /**
     * Todas as sessões
     */
    public function sessions(Request $request): JsonResponse
    {
        return response()->json([
            'upcoming' => $this->getUpcomingSessions($request->user()),
            'past'     => $this->getPastSessions($request->user()),
        ]);
    }

    /**
     * Próximas sessões
     */
    public function upcomingSessions(Request $request): JsonResponse
    {
        return response()->json([
            'sessions' => $this->getUpcomingSessions($request->user()),
        ]);
    }

    /**
     * Sessões anteriores
     */
    public function pastSessions(Request $request): JsonResponse
    {
        return response()->json([
            'sessions' => $this->getPastSessions($request->user()),
        ]);
    }

    /**
     * Agendar nova sessão
     */
    public function scheduleSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'     => 'required|date|after:today',
            'time'     => 'required|string',
            'topic'    => 'required|string|max:255',
            'duration' => 'required|in:30,45,60',
            'type'     => 'required|in:video,chat',
        ]);

        return response()->json([
            'message' => 'Sessão agendada com sucesso!',
            'session' => array_merge($validated, [
                'id'     => rand(100, 999),
                'status' => 'scheduled',
            ]),
        ], 201);
    }

    /**
     * Reagendar sessão
     */
    public function rescheduleSession(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date|after:today',
            'time' => 'required|string',
        ]);

        return response()->json([
            'message'    => 'Sessão reagendada com sucesso!',
            'session_id' => $id,
            'new_date'   => $validated['date'],
            'new_time'   => $validated['time'],
        ]);
    }

    /**
     * Avaliar sessão passada
     */
    public function rateSession(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'notes'  => 'sometimes|string|max:500',
        ]);

        return response()->json([
            'message'    => 'Avaliação registrada com sucesso!',
            'session_id' => $id,
            'rating'     => $validated['rating'],
        ]);
    }

    private function getMentor(): array
    {
        return [
            'id'            => 1,
            'name'          => 'Dr. Carlos Silva',
            'role'          => 'Mentor Sênior',
            'expertise'     => 'Estratégia de Negócios & Inovação',
            'rating'        => 4.9,
            'totalSessions' => 250,
            'avatar'        => null,
            'bio'           => 'Especialista em estratégia empresarial com mais de 20 anos de experiência.',
        ];
    }

    private function getUpcomingSessions($user): array
    {
        return [
            [
                'id'       => 1,
                'date'     => '2026-05-15',
                'time'     => '15:00',
                'duration' => '60 min',
                'topic'    => 'Revisão do Plano de Negócios',
                'type'     => 'video',
                'status'   => 'scheduled',
            ],
            [
                'id'       => 2,
                'date'     => '2026-05-22',
                'time'     => '10:00',
                'duration' => '45 min',
                'topic'    => 'Estratégias de Marketing Digital',
                'type'     => 'video',
                'status'   => 'scheduled',
            ],
        ];
    }

    private function getPastSessions($user): array
    {
        return [
            [
                'id'     => 10,
                'date'   => '2026-04-28',
                'topic'  => 'Análise SWOT e Posicionamento',
                'rating' => 5,
                'notes'  => 'Excelente sessão sobre identificação de oportunidades de mercado.',
            ],
            [
                'id'     => 9,
                'date'   => '2026-04-21',
                'topic'  => 'Definição de Missão e Visão',
                'rating' => 5,
                'notes'  => 'Esclarecimentos importantes sobre propósito e direcionamento estratégico.',
            ],
            [
                'id'     => 8,
                'date'   => '2026-04-14',
                'topic'  => 'Apresentação Inicial',
                'rating' => 4,
                'notes'  => 'Primeira sessão de mentoria. Definimos objetivos e expectativas.',
            ],
        ];
    }
}
