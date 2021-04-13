<?php


namespace App\Traits;


trait ResponseTrait
{
    public function responseSuccess($data = [], $message = 'success', $code = 0)
    {
        return json_encode([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function responseError($message = 'error', $data = [], $httpStatusCode = 500, $code = 500)
    {
        return json_encode([
            'code' => $code,
            'message' => $message,
            'error' => $data,
        ], $httpStatusCode);
    }
}