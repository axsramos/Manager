<?php

namespace App\Shared;

use App\Core\AuthSession;
use  App\Shared\MessageCodeHttp;
use  App\Shared\MessageCodeEn;
use  App\Shared\MessageCodePt;
use  App\Shared\MessageCodeManager;

class MessageDictionary
{
    const TYPE_MESSAGE_SUCCESS = 'SUCCESS';
    const TYPE_MESSAGE_INFORMATION = 'INFO';
    const TYPE_MESSAGE_WARNING = 'WARNING';
    const TYPE_MESSAGE_ERROR = 'DANGER';
    private $message;


    public function getMessage(int $code, $title = 'Message', $description = '')
    {

        // default:
        $this->message['Code'] = $code;
        $this->message['Type'] = self::TYPE_MESSAGE_INFORMATION;
        $this->message['Title'] = $title;
        $this->message['Description'] = $description;

        if ($code >= 0 && $code <= 3) {
            switch ($code) {
                case 0:
                    $this->message['Code'] = $code;
                    $this->message['Type'] = self::TYPE_MESSAGE_SUCCESS;
                    $this->message['Title'] = $title;
                    $this->message['Description'] = $description;
                    break;
                case 1:
                    $this->message['Code'] = $code;
                    $this->message['Type'] = self::TYPE_MESSAGE_ERROR;
                    $this->message['Title'] = $title;
                    $this->message['Description'] = $description;
                    break;
                case 2:
                    $this->message['Code'] = $code;
                    $this->message['Type'] = self::TYPE_MESSAGE_WARNING;
                    $this->message['Title'] = $title;
                    $this->message['Description'] = $description;
                    break;
                case 3:
                    $this->message['Code'] = $code;
                    $this->message['Type'] = self::TYPE_MESSAGE_INFORMATION;
                    $this->message['Title'] = $title;
                    $this->message['Description'] = $description;
                    break;
            }
        }

        if ($code >= 100 && $code <= 599) {
            $result = MessageCodeHttp::getMessage($code, $this);
            $this->message['Code'] = $result['Code'];
            $this->message['Type'] = self::TYPE_MESSAGE_INFORMATION;
            $this->message['Title'] = $result['Message'];
            $this->message['Description'] = $result['Description'];
        }

        if ($code >= 600 && $code <= 999) {
            $lng = AuthSession::get()['LANGUAGE'] ?? 'pt';
            $result = MessageCodeManager::getMessage($code, $lng, $this);
            $this->message['Code'] = $result['Code'];
            $this->message['Type'] = $result['Type'];
            $this->message['Title'] = $result['Title'];
            $this->message['Description'] = $result['Description'];
        }

        if ($code >= 1000) {

            if ((AuthSession::get()['LANGUAGE'] ?? 'pt') == 'pt') {
                $this->message = MessageCodePt::getMessage($code, $this);
            } else {
                $this->message = MessageCodeEn::getMessage($code, $this);
            }
        }

        return $this->message;
    }
}
