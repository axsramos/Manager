<?php

namespace App\Shared;

class MessageCodePt
{
    public $message = array();

    public static function getMessage(int $code, $msg)
    {
        self::$message['Code'] = $code;
        self::$message['Type'] = $msg::TYPE_MESSAGE_INFORMATION;
        self::$message['Title'] = '';
        self::$message['Description'] = '';

        if ($code <= 99) {
            // // TYPE_MESSAGE_SUCCESS //
            // switch ($code) {
            //     case 99:
            //     self::$message['Code'] = $code;
            //     self::$message['Type'] = $msg::TYPE_MESSAGE_SUCCESS;
            //     self::$message['Title'] = '';
            //     self::$message['Description'] = '';
            // }

            // // TYPE_MESSAGE_INFORMATION //
            // switch ($code) {
            //     case 99:
            //     self::$message['Code'] = $code;
            //     self::$message['Type'] = $msg::TYPE_MESSAGE_INFORMATION;
            //     self::$message['Title'] = '';
            //     self::$message['Description'] = '';
            // }

            // // TYPE_MESSAGE_WARNING //
            // switch ($code) {
            //     case 99:
            //     self::$message['Code'] = $code;
            //     self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
            //     self::$message['Title'] = '';
            //     self::$message['Description'] = '';
            // }

            // // TYPE_MESSAGE_ERROR //
            // switch ($code) {
            //     case 99:
            //     self::$message['Code'] = $code;
            //     self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
            //     self::$message['Title'] = '';
            //     self::$message['Description'] = '';
            // }
        }

        return self::$message;
    }
}
