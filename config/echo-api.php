<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Responses for errors
    |--------------------------------------------------------------------------
    */

    'errors' => [
        'EXAMPLE ' => [ // Error code name (int|string).
            'message' => 'Example of error data structure', // Error message (string).
            'http' => [
                'code' => 400, // HTTP status code for the response (int).
                'headers' => null, // HTTP headers for the response (array|null).
            ],
            'data' => null // Array with additional data for the response (array|null).
        ],
        'EXTENDED_EXAMPLE' => [
            'message' => 'Example of error data structure',
            'http' => [
                'code' => 400,
                'headers' => [
                    'x-foo' => true
                ],
            ],
            'data' => [
                'error' => [ // The data from this array will be included in the response error array.
                    'count' => 0
                ],
                'status' => 400
            ]
        ],
    ],
];
