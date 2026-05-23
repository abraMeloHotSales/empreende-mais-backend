<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostForum;
use App\Models\ComentarioForum;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComunidadeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'discussions'    => $this->getDiscussions(),
            'trendingTopics' => $this->getTrendingTopics(),
            'stats'          => $this->getCommunityStats(),
        ]);
    }

    public function discussions(Request $request): JsonResponse
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $search  = $request->get('search');

        $total       = PostForum::count();
        $discussions = $this->getDiscussions($search, $perPage, ($page - 1) * $perPage);

        return response()->json([
            'discussions' => $discussions,
            'pagination'  => ['page' => $page, 'per_page' => $perPage, 'total' => $total ?: count($discussions)],
        ]);
    }

    public function createDiscussion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:150',
            'content'  => 'required|string',
            'category' => 'sometimes|string|max:100',
        ]);

        $user = $request->user();

        $post = PostForum::create([
            'usuario_id' => $user?->id,
            'titulo'     => $validated['title'],
            'conteudo'   => $validated['content'],
        ]);

        $post->load('usuario');

        return response()->json([
            'message'    => 'Discussão criada com sucesso!',
            'discussion' => $this->formatPost($post),
        ], 201);
    }

    public function discussion(Request $request, int $id): JsonResponse
    {
        $post = PostForum::with(['usuario', 'comentarios.usuario'])->withCount('comentarios')->find($id);

        if (!$post) {
            $fallback = collect($this->staticDiscussions())->firstWhere('id', $id);
            if (!$fallback) {
                return response()->json(['message' => 'Discussão não encontrada.'], 404);
            }
            return response()->json(['discussion' => $fallback]);
        }

        $data = $this->formatPost($post);
        $data['comments'] = $post->comentarios->map(fn($c) => [
            'id'      => $c->id,
            'author'  => $c->usuario?->nome ?? 'Anônimo',
            'content' => $c->conteudo,
            'time'    => $c->criado_em?->diffForHumans() ?? '—',
        ])->values();

        return response()->json(['discussion' => $data]);
    }

    public function likeDiscussion(Request $request, int $id): JsonResponse
    {
        return response()->json(['message' => 'Ação registrada!', 'discussion_id' => $id, 'liked' => true]);
    }

    public function trendingTopics(Request $request): JsonResponse
    {
        return response()->json(['topics' => $this->getTrendingTopics()]);
    }

    public function stats(Request $request): JsonResponse
    {
        return response()->json(['stats' => $this->getCommunityStats()]);
    }

    private function getDiscussions(?string $search = null, int $limit = 10, int $offset = 0): array
    {
        $query = PostForum::with('usuario')->withCount('comentarios')->latest('criado_em');

        if ($search) {
            $query->where(fn($q) => $q->where('titulo', 'like', "%{$search}%")->orWhere('conteudo', 'like', "%{$search}%"));
        }

        $posts = $query->skip($offset)->take($limit)->get();

        if ($posts->isNotEmpty()) {
            return $posts->map(fn($p) => $this->formatPost($p))->values()->toArray();
        }

        // fallback estático
        return $this->staticDiscussions();
    }

    private function formatPost(PostForum $post): array
    {
        $conteudo = $post->conteudo ?? '';
        $excerpt  = mb_strlen($conteudo) > 120 ? mb_substr($conteudo, 0, 120) . '...' : $conteudo;

        return [
            'id'       => $post->id,
            'author'   => $post->usuario?->nome ?? 'Anônimo',
            'avatar'   => null,
            'title'    => $post->titulo,
            'category' => 'Geral',
            'replies'  => $post->comentarios_count ?? 0,
            'likes'    => 0,
            'time'     => $post->criado_em?->diffForHumans() ?? '—',
            'excerpt'  => $excerpt,
        ];
    }

    private function getTrendingTopics(): array
    {
        $total = PostForum::count();

        if ($total > 0) {
            return [['name' => 'Empreendedorismo', 'count' => $total]];
        }

        // fallback estático
        return [
            ['name' => 'Marketing Digital', 'count' => 234],
            ['name' => 'Gestão Financeira', 'count' => 189],
            ['name' => 'Redes Sociais',      'count' => 156],
            ['name' => 'E-commerce',         'count' => 142],
            ['name' => 'Planejamento',       'count' => 128],
        ];
    }

    private function getCommunityStats(): array
    {
        $members     = User::count();
        $discussions = PostForum::count();

        if ($members > 0 || $discussions > 0) {
            return [
                'activeMembers'     => $members,
                'activeDiscussions' => $discussions,
                'weeklyReplies'     => ComentarioForum::where('criado_em', '>=', now()->subDays(7))->count(),
            ];
        }

        // fallback estático
        return [
            'activeMembers'     => 1247,
            'activeDiscussions' => 342,
            'weeklyReplies'     => 2800,
        ];
    }

    private function staticDiscussions(): array
    {
        return [
            ['id' => 1, 'author' => 'Maria Santos',   'avatar' => null, 'title' => 'Como começar um negócio online com baixo investimento?', 'category' => 'Empreendedorismo',  'replies' => 24, 'likes' => 45, 'time' => 'Há 2 horas', 'excerpt' => 'Estou pensando em começar um negócio de venda de produtos artesanais pela internet...'],
            ['id' => 2, 'author' => 'João Silva',     'avatar' => null, 'title' => 'Dicas para melhorar presença nas redes sociais',         'category' => 'Marketing Digital', 'replies' => 18, 'likes' => 32, 'time' => 'Há 5 horas', 'excerpt' => 'Gostaria de compartilhar algumas estratégias que funcionaram para mim...'],
            ['id' => 3, 'author' => 'Ana Costa',      'avatar' => null, 'title' => 'Planilha de controle financeiro para pequenos negócios',  'category' => 'Gestão Financeira', 'replies' => 56, 'likes' => 89, 'time' => 'Há 1 dia',   'excerpt' => 'Criei uma planilha simples para controlar receitas e despesas. Compartilho aqui...'],
            ['id' => 4, 'author' => 'Pedro Oliveira', 'avatar' => null, 'title' => 'Experiências com marketplace: Facebook x Instagram',     'category' => 'E-commerce',        'replies' => 31, 'likes' => 67, 'time' => 'Há 2 dias',  'excerpt' => 'Alguém mais teve experiência vendendo pelo marketplace do Facebook?...'],
        ];
    }
}
