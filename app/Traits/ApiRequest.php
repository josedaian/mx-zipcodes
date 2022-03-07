<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait ApiRequest
{
    /**
     * @param array $requestOptions 
     * @param string $data 
     * @return Response 
     * @throws Exception 
     */
    function apiGet(array $requestOptions, $data = []): Response {
        return Http::withHeaders($requestOptions['headers'])
            ->withOptions($requestOptions['options'])
            ->get($requestOptions['url'], $data);
    }

    /**
     * @param array $requestOptions 
     * @param array $data 
     * @return Response 
     * @throws Exception 
     */
    function apiPost(array $requestOptions, array $data = []): Response {
        return Http::withHeaders($requestOptions['headers'])
            ->withOptions($requestOptions['options'])
            ->post($requestOptions['url'], $data);
    }
}
