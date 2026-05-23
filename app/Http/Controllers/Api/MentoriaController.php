<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessaoMentoria;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MentoriaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'mentor'           => $this->getMentor($user),
            'upcomingSessions' => $this->getUpcomingSessions($user),
            'pastSessions'     => $this->getPastSessions($user),
        ]);
    }

    public function mentor(Request $request): JsonResponse
    {
        return response()->json(['mentor' => $this->getMentor($request->user())]);
    }

    public function sessions(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'upcoming' => $this->getUpcomingSessions($user),
            'past'     => $this->getPastSessions($user),
        ]);
    }

    public function upcomingSessions(Request $request): JsonResponse
    {
        return response()->json(['sessions' => $this->getUpcomingSessions($request->user())]);
    }

    public function pastSessions(Request $request): JsonResponse
    {
        return response()->json(['sessions' => $this->getPastSessions($request->user())]);
    }

    public function scheduleSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'      => 'required|date|after:today',
            'time'      => 'required|string',
            'topic'     => 'required|string|max:255',
            'duration'  => 'required|in:30,45,60',
            'type'      => 'required|in:video,chat',
            'mentor_id' => 'sometimes|integer|exists:usuarios,id',
        ]);

        $user     = $request->user();
        $mentorId = $validated['mentor_id']
            ?? User::where('tipo_usuario', 'mentor')->value('id');

        $sessao = SessaoMentoria::create([
            'mentor_id'   => $mentorId,
            'aluno_id'    => $user?->id,
            'agendado_em' => $validated['date'] . ' ' . $validated['time'] . ':00',
            'status'      => 'agendado',
            'observacoes' => $validated['topic'],
        ]);

        return response()->json([
            'message' => 'Sessão agendada com sucesso!',
            'session' => $this->formatSession($sessao),
        ], 201);
    }

    public function rescheduleSession(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date|after:today',
            'time' => 'required|string',
        ]);

        $sessao = SessaoMentoria::find($id);
        if ($sessao) {
            $sessao->agendado_em = $validated['date'] . ' ' . $validated['time'] . ':00';
            $sessao->save();
        }

        return response()->json([
            'message'    => 'Sessão reagendada com sucesso!',
            'session_id' => $id,
            'new_date'   => $validated['date'],
            'new_time'   => $validated['time'],
        ]);
    }

    public function rateSession(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'notes'  => 'sometimes|string|max:500',
        ]);

        $sessao = SessaoMentoria::find($id);
        if ($sessao) {
            $sessao->status      = 'realizado';
            $sessao->observacoes = $validated['notes'] ?? $sessao->observacoes;
            $sessao->save();
        }

        return response()->json([
            'message'    => 'Avaliação registrada com sucesso!',
            'session_id' => $id,
            'rating'     => $validated['rating'],
        ]);
    }

    private function getMentor($user): array
    {
        if ($user) {
            $sessao = SessaoMentoria::with('mentor.perfilMentor')
                ->where('aluno_id', $user->id)
                ->whereNotNull('mentor_id')
                ->latest('agendado_em')
                ->first();

            $mentor = $sessao?->mentor
                ?? User::with('perfilMentor')->where('tipo_usuario', 'mentor')->first();

            if ($mentor) {
                $pm = $mentor->perfilMentor;
                return [
                    'id'            => $mentor->id,
                    'name'          => $mentor->nome,
                    'role'          => 'Mentor',
                    'expertise'     => $pm?->nivel_instrucao ?? '—',
                    'rating'        => null,
                    'totalSessions' => SessaoMentoria::where('mentor_id', $mentor->id)->count(),
                    'avatar'        => null,
                    'bio'           => $mentor->perfil?->bio ?? '—',
                ];
            }
        }

        // fallback estático
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
        if ($user) {
            $sessoes = SessaoMentoria::where('aluno_id', $user->id)
                ->where('agendado_em', '>=', now())
                ->orderBy('agendado_em')
                ->get();

            if ($sessoes->isNotEmpty()) {
                return $sessoes->map(fn($s) => $this->formatSession($s))->values()->toArray();
            }
        }

        // fallback estático
        return [
            ['id' => 1, 'date' => '2026-05-15', 'time' => '15:00', 'duration' => '60 min', 'topic' => 'Revisão do Plano de Negócios',      'type' => 'video', 'status' => 'scheduled'],
            ['id' => 2, 'date' => '2026-05-22', 'time' => '10:00', 'duration' => '45 min', 'topic' => 'Estratégias de Marketing Digital', 'type' => 'video', 'status' => 'scheduled'],
        ];
    }

    private function getPastSessions($user): array
    {
        if ($user) {
            $sessoes = SessaoMentoria::where('aluno_id', $user->id)
                ->where('agendado_em', '<', now())
                ->orderByDesc('agendado_em')
                ->get();

            if ($sessoes->isNotEmpty()) {
                return $sessoes->map(fn($s) => [
                    'id'     => $s->id,
                    'date'   => $s->agendado_em?->toDateString(),
                    'topic'  => $s->observacoes ?? '—',
                    'rating' => null,
                    'notes'  => $s->observacoes,
                ])->values()->toArray();
            }
        }

        // fallback estático
        return [
            ['id' => 10, 'date' => '2026-04-28', 'topic' => 'Análise SWOT e Posicionamento',   'rating' => 5, 'notes' => 'Excelente sessão sobre identificação de oportunidades de mercado.'],
            ['id' => 9,  'date' => '2026-04-21', 'topic' => 'Definição de Missão e Visão',      'rating' => 5, 'notes' => 'Esclarecimentos importantes sobre propósito e direcionamento estratégico.'],
            ['id' => 8,  'date' => '2026-04-14', 'topic' => 'Apresentação Inicial',             'rating' => 4, 'notes' => 'Primeira sessão de mentoria. Definimos objetivos e expectativas.'],
        ];
    }

    private function formatSession(SessaoMentoria $s): array
    {
        return [
            'id'       => $s->id,
            'date'     => $s->agendado_em?->toDateString(),
            'time'     => $s->agendado_em?->format('H:i'),
            'duration' => '—',
            'topic'    => $s->observacoes ?? '—',
            'type'     => 'video',
            'status'   => $s->status,
        ];
    }
}
