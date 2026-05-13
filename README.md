# Empreende+ — Backend API

Backend RESTful construído com **PHP 8.2** e **Laravel 11** para a plataforma Empreende+.

---

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) instalado e rodando

> Não é necessário ter PHP ou Composer instalados localmente.

---

## Como rodar (com Docker)

```bash
# 1. Entrar na pasta do backend
cd empreendeMaisBackend

# 2. Subir o container (primeira vez faz o build automaticamente)
docker compose up --build
```

O servidor estará disponível em: **http://localhost:8000**

A partir da segunda vez, basta:
```bash
docker compose up
```

Para rodar em segundo plano (sem travar o terminal):
```bash
docker compose up -d
```

Para parar:
```bash
docker compose down
```

---

## Rodar o projeto completo (front + back)

Abra **dois terminais**:

**Terminal 1 — Backend:**
```bash
cd empreendeMaisBackend
docker compose up
```

**Terminal 2 — Frontend:**
```bash
cd empreendeMaisMobile
npm run dev
```

Acesse **http://localhost:5173** no navegador.

| Serviço | URL |
|---|---|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8000/api |

---

## Endpoints da API

Todas as rotas são prefixadas com `/api`.

### Autenticação
| Método | Rota | Descrição |
|---|---|---|
| POST | `/api/auth/register` | Registrar novo usuário |
| POST | `/api/auth/login` | Login |
| GET | `/api/auth/me` | Usuário autenticado |
| POST | `/api/auth/logout` | Logout |

### Dashboard
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/dashboard` | Dados completos do dashboard |
| GET | `/api/dashboard/stats` | Estatísticas |
| GET | `/api/dashboard/activities` | Atividades recentes |
| GET | `/api/dashboard/next-steps` | Próximos passos |

### Jornada
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/jornada` | Jornada completa |
| GET | `/api/jornada/modules` | Lista de módulos |
| GET | `/api/jornada/modules/{id}` | Detalhes de um módulo |
| POST | `/api/jornada/modules/{id}/complete` | Concluir aula |

### Mentoria
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/mentoria` | Dados de mentoria |
| GET | `/api/mentoria/sessions/upcoming` | Próximas sessões |
| GET | `/api/mentoria/sessions/past` | Sessões anteriores |
| POST | `/api/mentoria/sessions` | Agendar sessão |
| PUT | `/api/mentoria/sessions/{id}/reschedule` | Reagendar sessão |
| POST | `/api/mentoria/sessions/{id}/rate` | Avaliar sessão |

### Comunidade
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/comunidade` | Dados da comunidade |
| GET | `/api/comunidade/discussions` | Lista de discussões |
| POST | `/api/comunidade/discussions` | Nova discussão |
| POST | `/api/comunidade/discussions/{id}/like` | Curtir discussão |
| GET | `/api/comunidade/topics/trending` | Tópicos em alta |

### Certificados
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/certificados` | Certificados e conquistas |
| GET | `/api/certificados/{id}/download` | Download do certificado |
| GET | `/api/certificados/achievements` | Conquistas |

### Configurações
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/configuracoes` | Configurações do usuário |
| PUT | `/api/configuracoes/profile` | Atualizar perfil |
| PUT | `/api/configuracoes/notifications` | Atualizar notificações |
| PUT | `/api/configuracoes/security` | Alterar senha |
| PUT | `/api/configuracoes/preferences` | Atualizar preferências |

---

## Estado atual

> **Fase de desenvolvimento:** todos os endpoints retornam **dados estáticos**.
> Nenhum banco de dados é necessário para rodar o projeto agora.

### Quando o banco de dados estiver pronto

1. Configurar banco no `.env` (SQLite ou MySQL)
2. Rodar as migrations (criar os arquivos em `database/migrations/`)
3. Reativar `auth:sanctum` nas rotas em `routes/api.php`
4. Reativar o middleware Sanctum em `bootstrap/app.php`
5. Substituir os arrays estáticos nos controllers por queries Eloquent

---

## Estrutura do projeto

```
empreendeMaisBackend/
├── app/
│   ├── Http/Controllers/Api/   # Controllers de cada módulo
│   └── Models/                 # User, Module, MentoringSession, Discussion, Certificate
├── bootstrap/
│   └── app.php                 # Configuração central do Laravel
├── config/
│   ├── cors.php                # CORS (permite requisições do frontend)
│   └── sanctum.php             # Autenticação por token
├── routes/
│   └── api.php                 # Todas as rotas da API
├── Dockerfile                  # Imagem PHP 8.2 com extensões necessárias
├── docker-compose.yml          # Orquestração do container
├── docker-entrypoint.sh        # Script de inicialização do container
└── .env.example                # Variáveis de ambiente (copiar para .env)
```

---

## Variáveis de ambiente relevantes

| Variável | Descrição | Padrão |
|---|---|---|
| `APP_URL` | URL do backend | `http://localhost:8000` |
| `FRONTEND_URL` | URL do frontend (CORS) | `http://localhost:5173` |
| `DB_CONNECTION` | Driver do banco (futuro) | `sqlite` |

---

## Integração com o Frontend

O frontend (`empreendeMaisMobile`) já está integrado com esta API.
A variável `VITE_API_URL=http://localhost:8000/api` no `.env` do frontend aponta para este backend.

Se o backend não estiver rodando, o frontend exibe dados estáticos de fallback automaticamente.
