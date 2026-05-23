<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JornadaController;
use App\Http\Controllers\Api\MentoriaController;
use App\Http\Controllers\Api\ComunidadeController;
use App\Http\Controllers\Api\CertificadosController;
use App\Http\Controllers\Api\ConfiguracoesController;

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register',        [AuthController::class, 'register']);
    Route::post('/login',           [AuthController::class, 'login']);
    Route::get('/me',               [AuthController::class, 'me']);
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::put('/profile',          [AuthController::class, 'updateProfile']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',  [AuthController::class, 'resetPassword']);
});

// Dashboard
Route::get('/dashboard',            [DashboardController::class, 'index']);
Route::get('/dashboard/stats',      [DashboardController::class, 'stats']);
Route::get('/dashboard/activities', [DashboardController::class, 'activities']);
Route::get('/dashboard/next-steps', [DashboardController::class, 'nextSteps']);

// Jornada de Formação
Route::prefix('jornada')->group(function () {
    Route::get('/',                       [JornadaController::class, 'index']);
    Route::get('/progress',               [JornadaController::class, 'progress']);
    Route::get('/modules',                [JornadaController::class, 'modules']);
    Route::get('/modules/{id}',           [JornadaController::class, 'module']);
    Route::post('/modules/{id}/complete', [JornadaController::class, 'completeLesson']);
});

// Mentoria
Route::prefix('mentoria')->group(function () {
    Route::get('/',                         [MentoriaController::class, 'index']);
    Route::get('/mentor',                   [MentoriaController::class, 'mentor']);
    Route::get('/sessions',                 [MentoriaController::class, 'sessions']);
    Route::get('/sessions/upcoming',        [MentoriaController::class, 'upcomingSessions']);
    Route::get('/sessions/past',            [MentoriaController::class, 'pastSessions']);
    Route::post('/sessions',                [MentoriaController::class, 'scheduleSession']);
    Route::put('/sessions/{id}/reschedule', [MentoriaController::class, 'rescheduleSession']);
    Route::post('/sessions/{id}/rate',      [MentoriaController::class, 'rateSession']);
});

// Comunidade
Route::prefix('comunidade')->group(function () {
    Route::get('/',                       [ComunidadeController::class, 'index']);
    Route::get('/discussions',            [ComunidadeController::class, 'discussions']);
    Route::post('/discussions',           [ComunidadeController::class, 'createDiscussion']);
    Route::get('/discussions/{id}',       [ComunidadeController::class, 'discussion']);
    Route::post('/discussions/{id}/like', [ComunidadeController::class, 'likeDiscussion']);
    Route::get('/topics/trending',        [ComunidadeController::class, 'trendingTopics']);
    Route::get('/stats',                  [ComunidadeController::class, 'stats']);
});

// Certificados e Conquistas
Route::prefix('certificados')->group(function () {
    Route::get('/',              [CertificadosController::class, 'index']);
    Route::get('/list',          [CertificadosController::class, 'certificates']);
    Route::get('/{id}/download', [CertificadosController::class, 'download']);
    Route::get('/achievements',  [CertificadosController::class, 'achievements']);
    Route::get('/stats',         [CertificadosController::class, 'stats']);
});

// Configurações
Route::prefix('configuracoes')->group(function () {
    Route::get('/',                [ConfiguracoesController::class, 'index']);
    Route::put('/profile',         [ConfiguracoesController::class, 'updateProfile']);
    Route::put('/notifications',   [ConfiguracoesController::class, 'updateNotifications']);
    Route::put('/security',        [ConfiguracoesController::class, 'updateSecurity']);
    Route::put('/preferences',     [ConfiguracoesController::class, 'updatePreferences']);
});
