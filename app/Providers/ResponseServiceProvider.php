<?php


namespace App\Providers;


use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use App\Entities\ResponseMessage;

class ResponseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 自定义响应格式
        Response::macro('wrap', function ($result, int $code = ResponseMessage::SUCCESS_CODE, string $message = ResponseMessage::SUCCESS_MESSAGE) {
            $data = [
                'code'      => $code,
                'message'   => $message,
                'requestId' => '',
                'data'      => $result,
            ];

            return Response::json($data);
        });

    }
}
