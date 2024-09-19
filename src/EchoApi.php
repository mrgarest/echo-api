<?php

namespace MrGarest\EchoApi;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use MrGarest\EchoApi\Exceptions as Ex;

class EchoApi
{
    /**
     * Successful response in JSON format.
     *
     * @param array|null $data Additional data that may be included in the response (optional).
     * @param int $httpStatus HTTP status code for the error (optional).
     * @param array|null $httpHeaders HTTP headers for the response (optional).
     *
     * @return JsonResponse
     */
    public static function success(array|null $data = null, int $httpStatus = Response::HTTP_OK, array|null $httpHeaders = null): JsonResponse
    {
        $response = [
            'success' => true,
        ];
        if (!empty($data)) $response = array_merge($response, $data);

        return static::asJson($response, $httpStatus, $httpHeaders);
    }

    /**
     * Response with an error in JSON format.
     *
     * @param int|string $code Error code identifier.
     * @param string|null $message Error message.
     * @param array|null $data Additional data that may be included in the response (optional).
     * @param int $httpStatus HTTP status code for the error (optional).
     * @param array|null $httpHeaders HTTP headers for the response (optional).
     * 
     * @return JsonResponse
     */
    public static function error(int|string $code, string|null $message, array|null $data = null, int $httpStatus = Response::HTTP_BAD_REQUEST, array|null $httpHeaders = null): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if (!empty($data) && isset($data['error'])) {
            $response['error'] = array_merge($response['error'], $data['error']);
            unset($data['error']);
        }
        if (!empty($data)) $response = array_merge($response, $data);

        return static::asJson($response, $httpStatus, $httpHeaders);
    }

    /**
     * Response with HTTP error code in JSON format.
     *
     * @param int $httpStatus HTTP status code for the response.
     * @param array|null $data Additional data that may be included in the response (optional).
     * @param array|null $httpHeaders HTTP headers for the response (optional).
     * 
     * @return JsonResponse
     * @throws Ex\HttpStatusCodeExistException
     */
    public static function httpError(int $httpStatus, array|null $data = null, $httpHeaders = null): JsonResponse
    {
        if (!isset(Response::$statusTexts[$httpStatus])) throw new Ex\HttpStatusCodeExistException();
        return static::error($httpStatus, Response::$statusTexts[$httpStatus], $data, $httpStatus, $httpHeaders);
    }

    /**
     * Validation error response in JSON format.
     *
     * @param \Illuminate\Validation\Validator $validator The Laravel Validator instance.
     *
     * @return JsonResponse|null
     */
    public static function validatorError(\Illuminate\Validation\Validator $validator): JsonResponse|null
    {
        if (!$validator->fails()) return null;
        $validatorErrors = [];
        foreach ($validator->errors()->toArray() as $key => $value) $validatorErrors[] =  [
            'field' => $key,
            'message' => isset($value[0]) ? $value[0] : null
        ];

        $data['error']['validator'] = empty($validatorErrors) ? null : $validatorErrors;
        return static::error('VALIDATION_FAILED', 'The validation check failed.', $data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Finds the response with an error in the configuration file and outputs it in JSON format.
     *
     * @param int|string $code Error code identifier.
     * @param array|null $data Additional data that may be included in the response (optional).
     * @param array|null $httpHeaders HTTP headers for the response (optional).
     * 
     * @return JsonResponse
     * @throws Ex\ErrorCodeNotFoundException
     */
    public static function findError(int|string $code, array|null $data = null, array|null $httpHeaders = null): JsonResponse
    {
        $errorResponse = config('echo-api.errors');
        $error = isset($errorResponse[$code]) ? $errorResponse[$code] : null;
        if ($error === null) throw new Ex\ErrorCodeNotFoundException();

        $httpHeaders = static::getTernaryData($data, $error['data']);
        $httpHeaders = static::getTernaryData($httpHeaders, $error['http']['headers']);

        return static::error($code, $error['message'], $data, $error['http']['code'], $httpHeaders);
    }

    /**
     * Converts a data array to a JSON response.
     *
     * @param array $data Data to be included in the JSON response.
     * @param int $httpStatus HTTP status code for the response (optional).
     * @param array|null $httpHeaders HTTP headers for the response (optional).
     * 
     * @return JsonResponse
     */
    protected static function asJson(array $data, int $httpStatus = Response::HTTP_OK, array|null $httpHeaders = null): JsonResponse
    {
        return response()->json($data, $httpStatus, $httpHeaders === null ? [] : $httpHeaders);
    }

    protected static function getTernaryData($data1, $data2, $elser = null)
    {
        return !empty($data1) ? $data1 : (!empty($data2) ? $data2 : $elser);
    }
}
