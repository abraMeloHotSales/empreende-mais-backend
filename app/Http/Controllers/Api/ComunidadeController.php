<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// TODO (quando banco disponível): substituir arrays estáticos por Discussion::latest()->paginate()

class ComunidadeController extends Controller
{
    /**
     * Dados completos da comunidade
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'discussions'    => $this->getDiscussions(),
            'trendingTopics' => $this->getTrendingTopics(),
            'stats'          => $this->getCommunityStats(),
        ]);
    }

    /**
     * Lista de discussões (com paginação)
     */
    public function discussions(Request $request): JsonResponse
    {
        $page     = $request->get('page', 1);
        $perPage  = $request->get('per_page', 10);
        $category = $request->get('category');

        $discussions = collect($this->getDiscussions());

        if ($category) {
            $discussions = $discussions->filter(fn ($d) => $d['category'] === $category);
        }

        return response()->json([
            'discussions' => $discussions->values(),
            'pagination'  => [
                'page'     => (int) $page,
                'per_page' => (int) $perPage,
                'total'    => $discussions->count(),
            ],
        ]);
    }

    /**
     * Criar nova discussão
     */
    public function createDiscussion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        return response()->json([
            'message'    => 'Discussão criada com sucesso!',
            'discussion' => array_merge($validated, [
                'id'      => rand(100, 999),
                'author'  => $request->user()->name,
                'replies' => 0,
                'likes'   => 0,
                'time'    => 'Agora mesmo',
            ]),
        ], 201);
    }

    /**
     * Detalhes de uma discussão
     */
    public function discussion(Request $request, int $id): JsonResponse
    {
        $discussion = collect($this->getDiscussions())->firstWhere('id', $id);

        if (!$discussion) {
            return response()->json(['message' => 'Discussão não encontrada.'], 404);
        }

        return response()->json(['discussion' => $discussion]);
    }

    /**
     * Curtir/descurtir uma discussão
     */
    public function likeDiscussion(Request $request, int $id): JsonResponse
    {
        return response()->json([
            'message'       => 'Ação registrada!',
            'discussion_id' => $id,
            'liked'         => true,
            'likes'         => rand(10, 100),
        ]);
    }

    /**
     * Tópicos em alta
     */
    public function trendingTopics(Request $request): JsonResponse
    {
        return response()->json([
            'topics' => $this->getTrendingTopics(),
        ]);
    }

    /**
     * Estatísticas da comunidade
     */
    public function stats(Request $request): JsonResponse
    {
        return response()->json([
            'stats' => $this->getCommunityStats(),
        ]);
    }

    private function getDiscussions(): array
    {
        return [
            [
                'id'       => 1,
                'author'   => 'Maria Santos',
                'avatar'   => null,
                'title'    => 'Como começar um negócio online com baixo investimento?',
                'category' => 'Empreendedorismo',
                'replies'  => 24,
                'likes'    => 45,
                'time'     => 'Há 2 horas',
                'excerpt'  => 'Estou pensando em começar um negócio de venda de produtos artesanais pela internet...',
            ],
            [
                'id'       => 2,
                'author'   => 'João Silva',
                'avatar'   => null,
                'title'    => 'Dicas para melhorar presença nas redes sociais',
                'category' => 'Marketing Digital',
                'replies'  => 18,
                'likes'    => 32,
                'time'     => 'Há 5 horas',
                'excerpt'  => 'Gostaria de compartilhar algumas estratégias que funcionaram para mim...',
            ],
            [
                'id'       => 3,
                'author'   => 'Ana Costa',
                'avatar'   => null,
                'title'    => 'Planilha de controle financeiro para pequenos negócios',
                'category' => 'Gestão Financeira',
                'replies'  => 56,
                'likes'    => 89,
                'time'     => 'Há 1 dia',
                'excerpt'  => 'Criei uma planilha simples para controlar receitas e despesas. Compartilho aqui...',
            ],
            [
                'id'       => 4,
                'author'   => 'Pedro Oliveira',
                'avatar'   => null,
                'title'    => 'Experiências com marketplace: Facebook x Instagram',
                'category' => 'E-commerce',
                'replies'  => 31,
                'likes'    => 67,
                'time'     => 'Há 2 dias',
                'excerpt'  => 'Alguém mais teve experiência vendendo pelo marketplace do Facebook?...',
            ],
        ];
    }

    private function getTrendingTopics(): array
    {
        return [
            ['name' => 'Marketing Digital',  'count' => 234],
            ['name' => 'Gestão Financeira',  'count' => 189],
            ['name' => 'Redes Sociais',       'count' => 156],
            ['name' => 'E-commerce',          'count' => 142],
            ['name' => 'Planejamento',        'count' => 128],
        ];
    }

    private function getCommunityStats(): array
    {
        return [
            'activeMembers'      => 1247,
            'activeDiscussions'  => 342,
            'weeklyReplies'      => 2800,
        ];
    }
}
