<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class PublicException extends Exception
{
    const HINT_DB_DUPLICATE = 'db.duplicate';
    private $attr;

    function __construct(array $attr){
        $this->attr = array_merge([
            'text' => 'NO_TEXT',
            'infoCode' => null,
            'exCode' => 0,
            'httpCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'hintCode' => null,
            'data' => null
        ], $attr);

        parent::__construct(
            $this->attr['text'],
            $this->attr['exCode'],
            isset($attr['exception']) && is_a($attr['exception'], Throwable::class) ? $attr['exception'] : null
        );
    }

    static function validationError( string $text, string $infoCode, int $httpCode = 400, array $attr=[]):self{
        return new PublicException(array_merge($attr, ['infoCode' => $infoCode, 'text' => $text, 'httpCode' => $httpCode]));
    }

    static function externalError( string $text, string $infoCode, int $httpCode = 400, array $attr=[]):self{
        return new PublicException(array_merge($attr,['infoCode' => 'external@'.$infoCode, 'text' => $text, 'httpCode' => $httpCode]) );
    }

    static function internalError( string $text, string $infoCode, int $httpCode = 500, array $attr=[]):self{
        return new PublicException(array_merge($attr,['infoCode' => $infoCode, 'text' => $text, 'httpCode' => $httpCode]));
    }

    static function fromException( \Throwable $exception, array $attr=[]):self{
        if ($exception instanceof PublicException) {
            return $exception;
        }

        $hintCode = null;

        if ($exception instanceof MethodNotAllowedHttpException) {
            $defaults = [
                'text' => 'El método especificado para la solicitud no es válido.',
                'infoCode' => 'bad_http_method',
                'httpCode' => Response::HTTP_METHOD_NOT_ALLOWED
            ];
        } else if ($exception instanceof NotFoundHttpException) {
            $defaults = [
                'text' => 'Recurso no existe (URL): '.request()->path(),
                'httpCode' => Response::HTTP_NOT_FOUND,
                'infoCode' => 'not_found'
            ];
        } else if ($exception instanceof HttpException) {
            $defaults = [
                'text' => $exception->getMessage(),
                'httpCode' => $exception->getStatusCode(),
                'infoCode' => 'form_error'
            ];
        } else if($exception instanceof AuthenticationException){
            $defaults = [
                'text' => __('No autorizado'),
                'infoCode' => 'auth.required'
            ];
        } else if ($exception instanceof QueryException) {
            $dbErrorInfo = null;

            $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $infoCode = 'db_error';
            $text = 'Error de base de datos';
            if( ($prev = $exception->getPrevious()) && ($prev instanceof PDOException) ){
                /** @var PDOException $prev */
                $dbErrorInfo = $prev->errorInfo;
            }

            if( $dbErrorInfo ){
                $sqlState = $dbErrorInfo[0];
                switch( $sqlState )
                {
                    case '23000':
                        if( isset($dbErrorInfo[1]) && \intval($dbErrorInfo[1]) === 1062){
                            $text .= ': Registro duplicado';
                            $infoCode .= '.duplicate';
                            $httpCode = Response::HTTP_BAD_REQUEST;
                            $hintCode = self::HINT_DB_DUPLICATE;
                        }
                        break;
                }
            }

            $defaults = [
                'text' => $text,
                'httpCode' => $httpCode,
                'infoCode' => $infoCode,
                'data' => [
                    'dbError' => $dbErrorInfo ? \implode(', ', $dbErrorInfo) : null
                ]
            ];
        } else {
            $defaults = [
                'text' => \get_class($exception).': '.$exception->getMessage().' en '.$exception->getFile().'@'.$exception->getLine(),
                'httpCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'infoCode' => 'exception.'.$exception->getCode()
            ];
        }

        $defaults['data']['type'] = \get_class($exception);
        $defaults['data']['file'] = $exception->getFile();
        $defaults['data']['line'] = $exception->getLine();
        $defaults['data']['trace'] = $exception->getTraceAsString();

        return new PublicException(array_merge($defaults,['hintCode' => $hintCode, 'exception' => $exception], $attr) );
    }

    /** @return null|string  */
    function getText():?string {
        return $this->attr['text'];
    }

    /** @return null|string  */
    function getHintCode():?string {
        return $this->attr['hintCode'];
    }

    /** @return null|string  */
    function getInfoCode():?string {
        return $this->attr['infoCode'];
    }

    /** @return int  */
    function getHttpCode():int {
        return $this->attr['httpCode'];
    }

    /** @return mixed  */
    function getExceptionCode() {
        return parent::getCode();
    }

    /** @return null|array  */
    function getData():?array {
        return $this->attr['data'];
    }
}
