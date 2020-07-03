<?php


namespace App\Exceptions;


use App\Entities\ResponseMessage;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class ExceptionHandler extends Handler
{
    // 自定义异常时的返回格式
    public function render($request, Exception $exception)
    {
        if (method_exists($exception, 'render') && $response = $exception->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($exception instanceof Responsable) {
            return $exception->toResponse($request);
        }

        $exception = $this->prepareException($exception);

        if ($exception instanceof HttpResponseException) {
            return Response::wrap('', ResponseMessage::FAIL_CODE, $exception->getMessage());
        } elseif ($exception instanceof AuthenticationException) {
            return Response::wrap('', ResponseMessage::FAIL_CODE, $exception->getMessage());
        } elseif ($exception instanceof ValidationException) {
            return Response::wrap('', ResponseMessage::FAIL_CODE, $exception->getMessage());
        }

        return Response::wrap('', ResponseMessage::FAIL_CODE, $exception->getMessage());
    }
}
