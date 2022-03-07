<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseBuilder {
    public function errorResponse($message = '', $code = 0, $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse {
        $response = [];
        $response['results'] = false;
        $response['infoCode'] = $code;
        $response['message'] = $message;

        return response()->json($response, $httpCode);
    }

    public function successResponse($data = [], $httpCode = Response::HTTP_OK): JsonResponse
    {
        $response = [];

        if(!empty($data)){
            $response = $data;
        }

        $headers = [
            'content-type'  => 'application/json',
            'cache-control' => 'no-cache',
        ];

        return response()->json($response, $httpCode, $headers);
    }
}
