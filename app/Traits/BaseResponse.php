<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait BaseResponse
{
    protected function success($data = [], $messages = [])
    {
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    protected function serverError($messages = [])
    {
        return response()->json([
            'success' => false,
            'messages' => $messages
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function notFound($messages = [])
    {
        return response()->json([
            'success' => false,
            'messages' => $messages,
            'params' => request()->all()
        ], Response::HTTP_NOT_FOUND);
    }

    protected function errorParams($messages = [])
    {
        return response()->json([
            'success' => false,
            'messages' => $messages,
            'params' => request()->all()
        ], Response::HTTP_BAD_REQUEST);
    }

    protected function permissionDenied($messages = [])
    {
        return response()->json([
            'success' => false,
            'messages' => $messages,
            'params' => request()->all()
        ], Response::HTTP_FORBIDDEN);
    }

    protected function baseResponse($result)
    {
        $response = '';
        switch ($result['status']) {
            case Response::HTTP_FORBIDDEN:
                $response = $this->permissionDenied($result['messages']);
                break;
            case Response::HTTP_OK:
                $response = $this->success($result['data'], $result['messages']);
                break;
            case Response::HTTP_INTERNAL_SERVER_ERROR:
                $response = $this->serverError($result['messages']);
                break;
            case Response::HTTP_NOT_FOUND:
                $response = $this->notFound($result['messages']);
                break;
            case Response::HTTP_BAD_REQUEST:
                $response = $this->errorParams($result['messages']);
                break;
        }
        return $response;
    }
}
