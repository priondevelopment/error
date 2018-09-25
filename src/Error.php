<?php
/*
|--------------------------------------------------
| Check Required Fields
|--------------------------------------------------
|
| Check required fields for entry
|
*/

namespace Error;

use App\Exceptions;

class Error {


    /**
     * Throw an Exception for Required Fields
     *
     * @param array $required
     * @return bool
     */
    public function required($required=[])
    {

        $error = false;
        $fields = [];
        $alerts = [];

        foreach ($required as $r) {
            $value = e(app('request')->input($r));
            if ($value == '') {
                $error = true;
                $fields[$r] = ['message' => 'Required'];
            }
        }

        if ($error) {
            $this->inputValidationException($fields);
        }

        return true;

    }


    /**
     * Throw an Input Validation Exception
     *
     * @param $fields
     */
    private function inputValidationException($fields)
    {
        $data = [
            'status' => 0,
            'error' => 1,
            'fields' => $fields,
            'message' => trans('errors.input.required'),
        ];
        throw new Error\JsonException(json_encode($data));
    }


    /**
     * Throw a Custom Json Exception
     *
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function message($message='')
    {
        $httpCode = $this->httpCode($code);
        $this->code(500, $message);
    }


    /**
     *  Throw a Json Exception from a Code (int)
     *
     *  Throw and Error and allow user to override the default message.
     */
    public function code($code=404, $message='')
    {
        if (!$message) {
            $message = trans('api.errors.' . $code);
        }

        $httpCode = $this->httpCode($code);
        throw new Error\Exceptions\JsonException(json_encode([
            'status' => 0,
            'error' => 1,
            'code' => $code,
            'message' => $message,
            'httpCode' => $httpCode,
        ]));

    }


    /**
     * Throw a Default Exception
     *
     * @param $code
     */
    public function exception($code=500, $message='') {

        if (!$message) {
            $message = trans('api.errors.' . $code);
        }

        throw new Error\Exceptions\JsonException(json_encode([
            'status' => 0,
            'error' => 1,
            'code' => $code,
            'message' => $message
        ]));
    }

    /**
     * Return a Default Error Code
     *
     * @param int $code
     * @return int
     */
    private function httpCode($code=500)
    {
        return ($code < 600) ? $code : 500;
    }

}