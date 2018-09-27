<?php

namespace Error\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Debug\Exception\FatalErrorException;

use App\Helpers;
use Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        JsonException::class,
        JsonErrorException::class,
        CodeException::class,
        CustomException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof JsonException) {
            return $this->jsonException($e);

        } elseif ($e instanceof JsonErrorException) {
            return $this->jsonErrorException($e);

        } elseif ($e instanceof CodeException) {
            return app()->make('error')->code($e->getMessage());

        } elseif ($e instanceof CustomException) {
            return app()->make('error')->message($e->getMessage());

        } elseif ($e instanceof NotFoundHttpException){
            return $this->error404();

        } elseif ($e instanceof MethodNotAllowedHttpException) {
            // Do Not Throw an Exception for this

        } elseif ($e instanceof FatalErrorException) {
            if (config('prionerror.jsonDefaultError')) {
                return $this->fatalErrorException($e);
            }
        }

        return parent::render($request, $e);
    }


    /**
     * Return a Json Exception
     *
     * @param $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function jsonException($e, $httpCode=200)
    {
        $array = json_decode($e->getMessage(), true);

        if (isset($array['httpCode'])) {
            $httpCode = $array['httpCode'];
            unset($array['httpCode']);
        }

        return response()->json($array, $httpCode);
    }


    /**
     * Return a 500 Json error
     *
     * @param $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function jsonErrorException($e)
    {
        return $this->jsonException($e, 500);
    }


    /**
     * Return a 4040 Error response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function error404()
    {
        return response()->json([
            'status' => 0,
            'error' => 1,
            'message' => trans('error::error.404'),
        ], 404);
    }


    /**
     * Handle Fatal Exceptions
     *
     * @param $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function fatalErrorException ($e) {
        if (app()->environment('local')) {
            $file_path = str_replace(base_path(), "", $e->getFile());
            return response()->json([
                'status' => 0,
                'error' => 1,
                'message' => $e->getMessage() . " in " . $file_path . " on line " . $e->getLine(),
            ], 500);
        }

        return response()->json([
            'status' => 0,
            'error' => 1,
            'message' => trans('errors.500'),
        ], 500);
    }
}
