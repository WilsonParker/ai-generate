<?php

namespace App\Exceptions;

use AIGenerate\Services\Exceptions\Facades\ExceptionCodeService;
use AIGenerate\Services\Exceptions\Loggers\Contracts\ExceptionServiceContract;
use App\Http\Response\Facades\ResponseTemplate;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        ValidationException::class,
        NotFoundHttpException::class,
        UnauthorizedException::class,
        AuthenticationException::class,
    ];
    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $throwable) {
            $service = app()->make(ExceptionServiceContract::class);
            $service->log($throwable);
        });

        $this->renderable(function (Throwable $throwable) {
            return ResponseTemplate::toJson(
                null,
                Str::limit($throwable->getMessage(), 256),
                ExceptionCodeService::getCode($throwable),
            );
        });
    }

}
