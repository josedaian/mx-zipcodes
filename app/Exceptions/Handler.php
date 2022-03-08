<?php

namespace App\Exceptions;

use App\Traits\ApiResponseBuilder;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseBuilder;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });

        $this->renderable(function(Throwable $e, $request){
            return $this->handleException($e);
        });
    }

    /**
     * @param Throwable $exception
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function handleException(Throwable $exception)
    {
        $publicException = PublicException::fromException($exception);
        return $this->errorResponse($publicException->getText(), $publicException->getInfoCode(), $publicException->getHttpCode());
    }
}
