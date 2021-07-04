<?php

namespace App\Http\Traits;

Trait sendMessage
{
    // This function send the data if the request has been successful
    public function sendResponseData($result, $message, $code)
    {
        $response = [
            'status' => true,
            'data' => $result,
            'message' => $message
        ];

        return response()->json($response, $code);
    }

    // This function send a message if the request has been successful
    public function sendResponseMessage($message, $code)
    {
        $response = [
            'status' => true,
            'message' => $message
        ];

        return response()->json($response, $code);
    }

    // This function send an error or more if the request has been failed
    public function sendResponseError($message, $errorMessage = [], $code)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if(!empty($errorMessage))
            $response['errors'] = $errorMessage;

        return response()->json($response, $code);
    }
}
