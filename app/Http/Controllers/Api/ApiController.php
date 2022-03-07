<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseBuilder;
use Illuminate\Http\Request;

class ApiController extends Controller {
    use ApiResponseBuilder;

    protected function dispatchApiRequest(Request $request, $callback ) {
        try {
            ignore_user_abort(true);    // Api should not stop because connection closed
            return call_user_func($callback);

        } catch (\Throwable $unknown) {
            $publicException = PublicException::fromException( $unknown );
            \Log::error($this->getCallerClassAndMethod($publicException), ['error', $publicException]);
            return $this->errorResponse($publicException->getText(), $publicException->getInfoCode(), $publicException->getHttpCode());
        }
    }

    private function getCallerClassAndMethod(\Throwable $exception): string{
        $trace = $exception->getTrace();
        $childController = $trace[2];
        return $childController['class'].'@'.$childController['function'];
    }
}
