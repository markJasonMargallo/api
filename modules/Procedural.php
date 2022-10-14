<?php

/**
 * It returns a JSON string with a status code and a message.
 * 
 * @param errcode The HTTP response code.
 * 
 * @return A JSON object with the following structure:
 * {
 *     "status": {
 *         "remarks": "failed",
 *         "message": "Request Not Found."
 *     },
 *     "timestamp": "2017-03-20T11:00:00+08:00"
 * }
 */
function errorMessage($errcode)
{
    switch ($errcode) {
        case 400:
            $msg = "Bad Request. Please contact the systems administrator.";
            break;
        case 401:
            $msg = "Unauthorized user.";
            break;
        case 403:
            $msg = "Forbidden. Please contact the systems administrator.";
            break;
        default:
            $msg = "Request Not Found.";
            break;
    }
    http_response_code($errcode);
    return json_encode(array("status" => array("remarks" => "failed", "message" => $msg), "timestamp" => date_create()));
}

/**
 * It returns a JSON object with a status, payload, and timestamp.
 * 
 * @param payload The data that you want to return.
 * @param remarks This is the status of the response. It can be either success or error.
 * @param message The message to be displayed to the user.
 * @param code HTTP response code
 * 
 * @return Array The function response() is being returned.
 */

function response($payload, $code): array
{
    http_response_code($code);
    return array("payload" => $payload, "timestamp" => date_create());
}
