<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\TrilhaUsuario;
use App\Models\TrilhaAprendizagem;
use App\Models\SessaoMentoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificadosController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'certificates' => $this->getCertificates($user),
            'achievements' => $this->getAchievements($user),
            'stats'        => $this->getStats($user),
        ]);
    }

    public function certificates(Request $request): JsonResponse
    {
        return response()->json(['certificates' => $this->getCertificates($request->user())]);
    }

    public function download(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $cert = Certificado::where('id', $id)->when($user, fn($q) => $q->where('usuario_id', $user->id))->first();

        if (!$cert) {
            return response()->json(['message' => 'Certificado não disponível para download.'], 404);
        }

        return response()->json([
            'message'      => 'Download disponível!',
            'download_url' => $cert->url_certificado ?? url("/api/certificados/{$id}/pdf"),
        ]);
    }

    public function achievements(Request $request): JsonResponse
    {
        return response()->json(['achievements' => $this->getAchievements($request->user())]);
    }

    public function stats(Request $request): JsonResponse
    {
        return response()->json(['stats' => $this->getStats($request->user())]);
    }

    private function getCertificates($user): array
    {
        if ($user) {
            $certificados   = Certificado::with('trilha')->where('usuario_id', $user->id)->orderByDesc('emitido_em')->get();
            $trilhasUsuario = TrilhaUsuario::with('trilha')->where('usuario_id', $user->id)->get()->keyBy('trilha_id');

            if ($certificados->isNotEmpty() || $trilhasUsuario->isNotEmpty()) {
                $result = [];

                foreach ($certificados as $cert) {
                    $result[] = [
                        'id'        => $cert->id,
                        'title'     => $cert->trilha?->titulo ?? 'Trilha',
                        'module'    => 'Módulo Completo',
                        'issueDate' => $cert->emitido_em?->toDateString(),
                        'hours'     => null,
                        'status'    => 'completed',
                        'progress'  => 100,
                    ];
                }

                foreach ($trilhasUsuario as $tu) {
                    if (!$certificados->firstWhere('trilha_id', $tu->trilha_id)) {
                        $result[] = [
                            'id'        => null,
                            'title'     => $tu->trilha?->titulo ?? 'Trilha',
                            'module'    => $tu->status === 'concluido' ? 'Concluído' : 'Em andamento',
                            'issueDate' => null,
                            'hours'     => null,
                            'status'    => $tu->status === 'concluido' ? 'completed' : 'in-progress',
                            'progress'  => $tu->progresso ?? 0,
                        ];
                    }
                }

                return $result;
            }
        }

        // fallback estático
        return [
            ['id' => 1, 'title' => 'Planejamento Estratégico', 'module' => 'Módulo Completo', 'issueDate' => '2026-04-28', 'hours' => 40, 'status' => 'completed',   'progress' => 100],
            ['id' => 2, 'title' => 'Análise de Mercado',       'module' => 'Em andamento',    'issueDate' => null,         'hours' => 30, 'status' => 'in-progress', 'progress' => 65],
            ['id' => 3, 'title' => 'Modelo de Negócio',        'module' => 'Em andamento',    'issueDate' => null,         'hours' => 45, 'status' => 'in-progress', 'progress' => 30],
        ];
    }

    private function getAchievements($user): array
    {
        if ($user) {
            $certsCount   = Certificado::where('usuario_id', $user->id)->count();
            $sessoes      = SessaoMentoria::where('aluno_id', $user->id)->count();
            $trilhas      = TrilhaUsuario::where('usuario_id', $user->id)->count();
            $totalTrilhas = TrilhaAprendizagem::count();

            if ($trilhas > 0 || $sessoes > 0 || $certsCount > 0) {
                return [
                    ['icon' => '🎯', 'title' => 'Primeira Trilha',     'description' => 'Inicie sua primeira trilha de aprendizagem', 'earned' => $trilhas >= 1,    'date' => null],
                    ['icon' => '⭐', 'title' => 'Mentor Ativo',        'description' => 'Participe de 5 sessões de mentoria',         'earned' => $sessoes >= 5,    'progress' => $sessoes],
                    ['icon' => '🎓', 'title' => 'Mestre Empreendedor', 'description' => 'Complete todos os módulos',                  'earned' => $totalTrilhas > 0 && $certsCount >= $totalTrilhas, 'progress' => $totalTrilhas > 0 ? (int) round($certsCount / $totalTrilhas * 100) : 0],
                ];
            }
        }

        // fallback estático
        return [
            ['icon' => '🎯', 'title' => 'Primeiro Módulo',      'description' => 'Complete seu primeiro módulo',           'earned' => true,  'date' => '2026-04-28'],
            ['icon' => '🔥', 'title' => 'Sequência de 7 Dias',  'description' => 'Estude por 7 dias consecutivos',         'earned' => true,  'date' => '2026-04-25'],
            ['icon' => '⭐', 'title' => 'Mentor Ativo',         'description' => 'Participe de 5 sessões de mentoria',     'earned' => true,  'date' => '2026-04-21'],
            ['icon' => '💬', 'title' => 'Participante Ativo',   'description' => 'Faça 10 contribuições na comunidade',    'earned' => false, 'progress' => 7],
            ['icon' => '🎓', 'title' => 'Mestre Empreendedor',  'description' => 'Complete todos os módulos',              'earned' => false, 'progress' => 17],
            ['icon' => '🚀', 'title' => 'Aprendiz Dedicado',    'description' => 'Acumule 100 horas de estudo',            'earned' => false, 'progress' => 48],
        ];
    }

    private function getStats($user): array
    {
        if ($user) {
            $certsCount   = Certificado::where('usuario_id', $user->id)->count();
            $sessoes      = SessaoMentoria::where('aluno_id', $user->id)->count();
            $trilhas      = TrilhaUsuario::where('usuario_id', $user->id)->count();
            $totalTrilhas = TrilhaAprendizagem::count();

            if ($trilhas > 0 || $sessoes > 0 || $certsCount > 0) {
                $achievementsUnlocked = 0;
                if ($trilhas >= 1) $achievementsUnlocked++;
                if ($sessoes >= 5) $achievementsUnlocked++;
                if ($totalTrilhas > 0 && $certsCount >= $totalTrilhas) $achievementsUnlocked++;

                return [
                    'certificatesEarned'   => $certsCount,
                    'achievementsUnlocked' => $achievementsUnlocked,
                    'totalStudyHours'      => 0,
                ];
            }
        }

        // fallback estático
        return [
            'certificatesEarned'   => 1,
            'achievementsUnlocked' => 3,
            'totalStudyHours'      => 48,
        ];
    }
}
