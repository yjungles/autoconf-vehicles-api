<?php

use App\Http\Middleware\ForceJsonResponse;
use App\Support\ApiErrorResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->appendToGroup('api', ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, $_) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });

        $exceptions->render(function (ValidationException $e, $request) {
            return ApiErrorResponse::make(
                422,
                'Erro de validação',
                'Os dados informados são inválidos.',
                $e->errors()
            );
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            return ApiErrorResponse::make(
                401,
                'Não autenticado',
                'Autenticação é necessária.'
            );
        });

        $exceptions->render(function (UnauthorizedHttpException|AuthorizationException|AccessDeniedHttpException  $e, $request) {
            return ApiErrorResponse::make(
                403,
                'Não autorizado',
                'Você não tem permissão para acessar este recurso.'
            );
        });

        $exceptions->render(function (NotFoundHttpException|ModelNotFoundException $e, $request) {
            return ApiErrorResponse::make(
                404,
                'Recurso não encontrado',
                'O recurso solicitado não foi encontrado.'
            );
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, $request) {
            return ApiErrorResponse::make(
                429,
                'Muitas requisições',
                'O limite de requisições foi atingido. Tente novamente mais tarde.'
            );
        });

        $exceptions->render(function (HttpException $e, $request) {
            return ApiErrorResponse::make(
                $e->getStatusCode(),
                ' Erro HTTP',
                $e->getMessage() ?: 'Erro na requisição.'
            );
        });

        $exceptions->render(function (Throwable $e, $request) {
            return ApiErrorResponse::make(
                500,
                ' Erro Interno do Servidor',
                config('app.debug')
                    ? $e->getMessage()
                    : 'Erro inesperado do servidor.'
            );
        });

    })->create();
