<?php

namespace App\Http\Controllers\Helpers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ValidationHelper extends Controller
{
    public static function editPostValidations($allRequest)
    {
        $rules = [
            'itemId' => 'required|max:10',
            'itemTitle' => 'required|min:2|max:300',
            'itemDescription' => 'required|min:2|max:600',
            'itemDate' => 'required|min:2|max:50',
        ];
        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function deletePostValidations($allRequest)
    {
        $rules = [
            'itemId' => 'required|max:10',
        ];
        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    private static function _generateValidationErrorResponse($validator)
    {
        $errors = $validator->errors();
        $response = [];
        foreach ($errors->all() as $message) {
            $response[] = $message;
        }
        return [
            'error' => true,
            'type' => 'Validation Error',
            'response' => $response
        ];
    }
}
