<?php

namespace App\Http\Requests;

use App\Constants\Messages;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'success' => false,
                    'messages' => $validator->messages()->all(),
                    'params' => request()->all(),
                ],
                Response::HTTP_BAD_REQUEST
            )
        );
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()
                ->json(
                    [
                        'success' => false,
                        'messages' => Messages::RESPONSE_UNAUTHORIZED
                    ],
                    Response::HTTP_FORBIDDEN
                )
        );
    }

    public function getId()
    {
        return $this->route('id');
    }
}
