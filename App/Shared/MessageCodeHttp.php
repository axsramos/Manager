<?php

namespace App\Shared;

class MessageCodeHttp
{
    public static function getMessage(int $codeError)
    {
        $message = array();

        switch ($codeError) {
            // 1xx: Information //
            case 100:
                $message = array('Code' => $codeError, 'Message' => 'Continue', 'Description' => 'The server has received the request headers, and the client should proceed to send the request body');
                break;
            case 101:
                $message = array('Code' => $codeError, 'Message' => 'Switching Protocols', 'Description' => 'The requester has asked the server to switch protocols');
                break;
            case 103:
                $message = array('Code' => $codeError, 'Message' => 'Early Hints', 'Description' => 'Used with the Link header to allow the browser to start preloading resources while the server prepares a response');
                break;
            
            // 2xx: Successful //
            case 200:
                $message = array('Code' => $codeError, 'Message' => 'OK', 'Description' => 'The request is OK (this is the standard response for successful HTTP requests)');
                break;
            case 201:
                $message = array('Code' => $codeError, 'Message' => 'Created', 'Description' => 'The request has been fulfilled, and a new resource is created ');
                break;
            case 202:
                $message = array('Code' => $codeError, 'Message' => 'Accepted', 'Description' => 'The request has been accepted for processing, but the processing has not been completed');
                break;
            case 203:
                $message = array('Code' => $codeError, 'Message' => 'Non-Authoritative Information', 'Description' => 'The request has been successfully processed, but is returning information that may be from another source');
                break;
            case 204:
                $message = array('Code' => $codeError, 'Message' => 'No Content', 'Description' => 'The request has been successfully processed, but is not returning any content');
                break;
            case 205:
                $message = array('Code' => $codeError, 'Message' => 'Reset Content', 'Description' => 'The request has been successfully processed, but is not returning any content, and requires that the requester reset the document view');
                break;
            case 206:
                $message = array('Code' => $codeError, 'Message' => 'Partial Content', 'Description' => 'The server is delivering only part of the resource due to a range header sent by the client');
                break;

            // 3xx: Redirection //
            case 300:
                $message = array('Code' => $codeError, 'Message' => 'Multiple Choices', 'Description' => 'A link list. The user can select a link and go to that location. Maximum five addresses');
                break;
            case 301:
                $message = array('Code' => $codeError, 'Message' => 'Moved Permanently', 'Description' => 'The requested page has moved to a new URL');
                break;
            case 302:
                $message = array('Code' => $codeError, 'Message' => 'Found', 'Description' => 'The requested page has moved temporarily to a new URL');
                break;
            case 303:
                $message = array('Code' => $codeError, 'Message' => 'See Other', 'Description' => 'The requested page can be found under a different URL');
                break;
            case 304:
                $message = array('Code' => $codeError, 'Message' => 'Not Modified', 'Description' => 'Indicates the requested page has not been modified since last requested');
                break;
            case 307:
                $message = array('Code' => $codeError, 'Message' => 'Temporary Redirect', 'Description' => 'The requested page has moved temporarily to a new URL');
                break;
            case 308:
                $message = array('Code' => $codeError, 'Message' => 'Permanent Redirect', 'Description' => 'The requested page has moved permanently to a new URL');
                break;

            // 4xx: Client Error //
            case 400:
                $message = array('Code' => $codeError, 'Message' => 'Bad Request', 'Description' => 'The request cannot be fulfilled due to bad syntax');
                break;
            case 401:
                $message = array('Code' => $codeError, 'Message' => 'Unauthorized', 'Description' => 'The request was a legal request, but the server is refusing to respond to it. For use when authentication is possible but has failed or not yet been provided');
                break;
            case 402:
                $message = array('Code' => $codeError, 'Message' => 'Payment Required', 'Description' => 'Reserved for future use');
                break;
            case 403:
                $message = array('Code' => $codeError, 'Message' => 'Forbidden', 'Description' => 'The request was a legal request, but the server is refusing to respond to it');
                break;
            case 404:
                $message = array('Code' => $codeError, 'Message' => 'Not Found', 'Description' => 'The requested page could not be found but may be available again in the future');
                break;
            case 405:
                $message = array('Code' => $codeError, 'Message' => 'Method Not Allowed', 'Description' => 'A request was made of a page using a request method not supported by that page');
                break;
            case 406:
                $message = array('Code' => $codeError, 'Message' => 'Not Acceptable', 'Description' => 'The server can only generate a response that is not accepted by the client');
                break;
            case 407:
                $message = array('Code' => $codeError, 'Message' => 'Proxy Authentication Required', 'Description' => 'The client must first authenticate itself with the proxy');
                break;
            case 408:
                $message = array('Code' => $codeError, 'Message' => 'Request Timeout', 'Description' => 'The server timed out waiting for the request');
                break;
            case 409:
                $message = array('Code' => $codeError, 'Message' => 'Conflict', 'Description' => 'The request could not be completed because of a conflict in the request');
                break;
            case 410:
                $message = array('Code' => $codeError, 'Message' => 'Gone', 'Description' => 'The requested page is no longer available');
                break;
            case 411:
                $message = array('Code' => $codeError, 'Message' => 'Length Required', 'Description' => 'The "Content-Length" is not defined. The server will not accept the request without it');
                break;
            case 412:
                $message = array('Code' => $codeError, 'Message' => 'Precondition Failed', 'Description' => 'The precondition given in the request evaluated to false by the server');
                break;
            case 413:
                $message = array('Code' => $codeError, 'Message' => 'Request Too Large', 'Description' => 'The server will not accept the request, because the request entity is too large');
                break;
            case 414:
                $message = array('Code' => $codeError, 'Message' => 'Request-URI Too Long', 'Description' => 'The server will not accept the request, because the URI is too long. Occurs when you convert a POST request to a GET request with a long query information');
                break;
            case 415:
                $message = array('Code' => $codeError, 'Message' => 'Unsupported Media Type', 'Description' => 'The server will not accept the request, because the media type is not supported');
                break;
            case 416:
                $message = array('Code' => $codeError, 'Message' => 'Range Not Satisfiable', 'Description' => 'The client has asked for a portion of the file, but the server cannot supply that portion');
                break;
            case 417:
                $message = array('Code' => $codeError, 'Message' => 'Expectation Failed', 'Description' => 'The server cannot meet the requirements of the Expect request-header field');
                break;

            // 5xx: Server Error //
            case 500:
                $message = array('Code' => $codeError, 'Message' => 'Internal Server Error', 'Description' => 'A generic error message, given when no more specific message is suitable');
                break;
            case 501:
                $message = array('Code' => $codeError, 'Message' => 'Not Implemented', 'Description' => 'The server either does not recognize the request method, or it lacks the ability to fulfill the request');
                break;
            case 502:
                $message = array('Code' => $codeError, 'Message' => 'Bad Gateway', 'Description' => 'The server was acting as a gateway or proxy and received an invalid response from the upstream server');
                break;
            case 503:
                $message = array('Code' => $codeError, 'Message' => 'Service Unavailable', 'Description' => 'The server is currently unavailable (overloaded or down)');
                break;
            case 504:
                $message = array('Code' => $codeError, 'Message' => 'Gateway Timeout', 'Description' => 'The server was acting as a gateway or proxy and did not receive a timely response from the upstream server');
                break;
            case 505:
                $message = array('Code' => $codeError, 'Message' => 'HTTP Version Not Supported', 'Description' => 'The server does not support the HTTP protocol version used in the request');
                break;
            case 511:
                $message = array('Code' => $codeError, 'Message' => 'Network Authentication Required', 'Description' => 'The client needs to authenticate to gain network access');
                break;

            default:
                $message = array('Code' => $codeError, 'Message' => '', 'Description' => '');
                break;
        }

        return $message;
    }
}
