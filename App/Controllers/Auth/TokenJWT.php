<?php

namespace App\Controllers\Auth;

use App\Core\Controller;

// use App\Models\Cas\CasAppModel;
// use App\Models\Cas\CasPfuModel;
// use App\Models\Cas\CasPfiModel;
// use App\Models\Cas\CasUsrModel;
// use App\Models\Cas\CasRpaModel;
// use App\Models\Cas\CasRpsModel;
// use App\Models\Cas\CasRpuModel;
// use App\Models\Cas\CasTknModel;
// use App\Models\Cas\CasTusModel;
// use App\Shared\MessageDictionary;

class TokenJWT extends Controller
{
    // private const ACCEPTED_METHOD = ['POST', 'PUT'];
    private $data = array();
    // private $dataAuth = array();
    // private $messages = array();
    // private const APP_ISS = 'api.portalsiti.com.br';

    public function __construct()
    {
        // $this->message = new MessageDictionary();
    }

    public function index()
    {
        // $currentMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        // $methodValidation = $this->checkMethods($currentMethod, $this::ACCEPTED_METHOD, $this->message);

        // if ($methodValidation['Code'] != 0) {
        //     $this->view('jsonView', array("Message" => $methodValidation));
        //     return;
        // }

        // /**
        //  * Especialização do método
        //  */
        // switch ($currentMethod) {
        //     case 'POST':
        //         $this->methodPost();
        //         break;

        //     case 'PUT':
        //         $this->methodPut();
        //         break;
        // }

        // if (count($this->messages)) {
        //     $this->data['Messages'] = $this->messages;
        // }

        $this->view('jsonView', $this->data);
    }

    // private function methodPost()
    // {
    //     $payload = array();

    //     if ($this->getDataAuth()) {
    //         $inputPayload = json_decode($this->dataAuth[1]);
    //         $secret = $this->getSecretApp($inputPayload);
    //         if ($this->decode($this->dataAuth, $secret)) {
    //             $payload = $this->createPayload($inputPayload, $secret);
    //         } else {
    //             // Invalid signature.
    //             array_push($this->messages, $this->message->getMessage(1, 'Message', 'Assinatura inválida.'));
    //         }
    //     } else {
    //         // The authorization data is invalid.
    //         array_push($this->messages, $this->message->getMessage(1, 'Message', 'Os dados da autorização são inválidos.'));
    //     }

    //     if ($payload) {
    //         $this->data = $payload;
    //     }
    // }

    // private function methodPut()
    // {
    //     $token = array();

    //     if ($this->getDataAuth()) {
    //         $inputPayload = json_decode($this->dataAuth[1]);
    //         $secret = $this->getSecretApp($inputPayload);
    //         $token = $this->encode((array) $inputPayload, $secret);
    //     } else {
    //         // The authorization data is invalid.
    //         $this->data = $this->message->getMessage(1, 'Message', 'Os dados da autorização são inválidos.');
    //     }

    //     if ($token) {
    //         $this->data = array('token' => $token);
    //     }
    // }

    // private function getDataAuth()
    // {
    //     if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    //         $dataAuth = explode('.', $_SERVER['HTTP_AUTHORIZATION']);
    //         if (count($dataAuth) == 3) {
    //             $dataAuth[0] = str_replace('Bearer ', '', $dataAuth[0]);
    //             // header //
    //             $this->dataAuth[0] = static::base64_decode_url($dataAuth[0]);
    //             // payload //
    //             $this->dataAuth[1] = static::base64_decode_url($dataAuth[1]);
    //             // signature //
    //             $this->dataAuth[2] = $dataAuth[2];
    //             return true;
    //         }
    //     }

    //     /**
    //      * receiving the token in base64:
    //      * eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.
    //      * eyJzdWIiOiJqb2huLmRvZUB1b3Jhay5jb20iLCJhcHAiOiI2N2U2YTg0ODYwOTE1In0.
    //      * DxyDxdOSxF6fceHM6qrsCQ0mq6pvy2Fqt7kSYZ3gRTk
    //      * 
    //      * content after base64 decryption:
    //      * expectation of receipt [header]:
    //      * { "alg":"HS256", "typ":"JWT" }
    //      * 
    //      * expectation of receipt [payload]:
    //      * { "sub":"john.doe@uorak.com", "app":{{CasAppCod}}"" }
    //      * 
    //      * expectation of receipt [signature]:
    //      * {{CasAppKey}}
    //      */

    //     return false;
    // }

    // private static function base64url_encode($data)
    // {
    //     return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    // }

    // private static function base64_decode_url($string)
    // {
    //     return base64_decode(str_replace(['-', '_'], ['+', '/'], $string));
    // }

    // public static function encode(array $payload, string $secret): string
    // {

    //     $header = json_encode([
    //         "alg" => "HS256",
    //         "typ" => "JWT"
    //     ]);

    //     $payload = json_encode($payload);

    //     $header_payload = static::base64url_encode($header) . '.' . static::base64url_encode($payload);
    //     $signature = static::signatureHash($header_payload, $secret);

    //     return
    //         static::base64url_encode($header) . '.' .
    //         static::base64url_encode($payload) . '.' .
    //         static::base64url_encode($signature);
    // }

    // public static function decode($dataAuth, $secret)
    // {
    //     /**
    //      * header - dataAuth[0]
    //      * payload - dataAuth[1]
    //      * signature - dataAuth[2]
    //      */

    //     $header_payload = static::base64url_encode($dataAuth[0]) . '.' . static::base64url_encode($dataAuth[1]);
    //     $signature = static::signatureHash($header_payload, $secret);
        
    //     if (static::base64url_encode($signature) !== $dataAuth[2]) {
    //         // throw new \Exception('Invalid signature');
    //         return false;
    //     }

    //     return true;
    // }

    // private function createPayload($inputPayload, $token_key)
    // {
    //     $payload = array();

    //     if (isset($inputPayload->sub) && isset($inputPayload->app) && isset($inputPayload->rps)) {
    //         $user_mail_account = $inputPayload->sub;
    //         $app_used = $inputPayload->app;
    //         $rps_used = $inputPayload->rps;
    //     } else {
    //         return $payload;
    //     }

    //     $dtnow = new \DateTimeImmutable();
    //     $dtnbf = $dtnow;
    //     $dtexp = $dtnow->add(\DateInterval::createFromDateString('3600 seconds'));

    //     $email_domain = explode('@', $user_mail_account);

    //     if (count($email_domain) == 2) {
    //         // Data App //
    //         $payload['app'] = $app_used;

    //         // Data Repository //
    //         $obCasRpsModel = new CasRpsModel();
    //         $obCasRpsModel->setCasRpsCod($rps_used);
    //         if ($obCasRpsModel->readRegister()) {
    //             $payload['rps'] = $obCasRpsModel->getCasRpsCod();
    //             if ($obCasRpsModel->getCasRpsBlq() == 'S') {
    //                 $payload['rps-blocked'] = true;
    //             }
    //         }

    //         // Data User (CasUsr) //
    //         $obCasUsrModel = new CasUsrModel();
    //         $obCasUsrModel->setCasUsrDmn('@' . $email_domain[1]);
    //         $obCasUsrModel->setCasUsrLgn($email_domain[0]);
    //         if ($obCasUsrModel->existsAccount()) {
    //             $payload['sub'] = strtolower($user_mail_account);
    //             $payload['name'] = $obCasUsrModel->getCasUsrDsc();
    //             $payload['user'] = $obCasUsrModel->getCasUsrCod();
    //             if ($obCasUsrModel->getCasUsrBlq() == 'S') {
    //                 $payload['user-blocked'] = true;
    //             }
    //         }

    //         // Data Acesso (CasRpu) (CasTus) //
    //         $obCasRpuModel = new CasRpuModel();
    //         $obCasRpuModel->setCasRpsCod($rps_used);
    //         $obCasRpuModel->setCasUsrCod($obCasUsrModel->getCasUsrCod());
    //         if ($obCasRpuModel->readRegister()) {
    //             $obCasTusModel = new CasTusModel();
    //             $obCasTusModel->setCasRpsCod($rps_used);
    //             $obCasTusModel->setCasTusCod($obCasRpuModel->getCasTusCod());
    //             if ($obCasTusModel->readRegister()) {
    //                 $payload['home'] = $obCasTusModel->getCasTusLnk();
    //                 $payload['role'] = $obCasTusModel->getCasTusDsc();
    //             }
    //         }

    //         // Data Repository App (CasRpa) (CasPfu) //
    //         $obCasRpaModel = new CasRpaModel();
    //         $obCasRpaModel->setCasRpsCod($rps_used);
    //         $obCasRpaModel->setCasAppCod($app_used);
    //         if ($obCasRpaModel->readRegister()) {
    //             if ($obCasRpaModel->getCasRpaBlq() == 'N') {
    //                 $obCasPfuModel = new CasPfuModel();
    //                 $obCasPfuModel->setCasRpsCod($rps_used);
    //                 $obCasPfuModel->setCasUsrCod($obCasUsrModel->getCasUsrCod());
    //                 $resultCasPfu = $obCasPfuModel->readAllLinesForId();
    //                 $pfis = array();
    //                 if ($resultCasPfu) {
    //                     foreach ($resultCasPfu as $item) {
    //                         $obCasPfiModel = new CasPfiModel();
    //                         $obCasPfiModel->setCasRpsCod($rps_used);
    //                         $obCasPfiModel->setCasPfiCod($item['CasPfiCod']);
    //                         if ($obCasPfiModel->readRegister()) {
    //                             if ($obCasPfiModel->getCasPfiBlq() == 'N') {
    //                                 array_push($pfis, $item['CasPfiCod']);
    //                             }
    //                         }
    //                     }
    //                 }
    //                 $payload['permissions'] = json_encode($pfis);
    //             }
    //         }
            
    //         // Data Session //
    //         $token_user_full = $rps_used . $app_used . $obCasUsrModel->getCasUsrCod();
    //         $token_user = static::base64url_encode($token_user_full);
    //         $vCasTknCod = $token_user;

    //         $obCasTknModel = new CasTknModel();
    //         $obCasTknModel->setCasTknCod($vCasTknCod);
    //         if ($obCasTknModel->readRegister()) {
    //             $dtnow = new \DateTimeImmutable();
    //             $attCasTknKeyExp = $obCasTknModel->getCasTknKeyExp();
    //             if ($obCasTknModel->getCasTknBlq() == 'N') {
    //                 $expires = strtotime($attCasTknKeyExp, true);
    //                 if (is_null($attCasTknKeyExp) || ($expires >= $dtnow->getTimestamp())) {

    //                 }
    //             }
    //         }

    //         $payload['iss'] = static::APP_ISS;
    //         $payload['iat'] = $dtnow->getTimestamp();
    //         $payload['nbf'] = $dtnbf->getTimestamp();
    //         $payload['exp'] = $dtexp->getTimestamp();
    //         $payload['key'] = $token_user;
    //     }
        
    //     return $payload;
    // }

    // private function getToken($dataUser)
    // {
    //     /**
    //      * todo: buscar token na base de dados (CasTkn)
    //      */
    //     return $dataUser['key'] = uniqid();
    // }

    // // private function getSecretToken($inputPayload)
    // // {
    // //     $dataReturn = '';

    // //     $vCasAppKey = $this->getSecretApp($inputPayload);
    // //     $token = $this->encode((array) $inputPayload, $vCasAppKey);
    // //     $secret = explode('.', $token);

    // //     if (count($secret) == 3) {
    // //         $dataReturn = $secret[2];
    // //     }

    // //     return $dataReturn;
    // // }

    // private function getSecretApp($payload)
    // {
    //     $dtnow = new \DateTimeImmutable();

    //     if (isset($payload->app)) {
    //         $obCasAppModel = new CasAppModel();
    //         $obCasAppModel->setCasAppCod($payload->app);
    //         if ($obCasAppModel->readRegister()) {
    //             if ($obCasAppModel->getCasAppBlq() == 'N') {
    //                 $attCasAppKeyExp = $obCasAppModel->getCasAppKeyExp();
    //                 $expires = '';
    //                 if (! empty($attCasAppKeyExp)) {
    //                     $expires = strtotime($attCasAppKeyExp, true);
    //                 }
    //                 if (is_null($attCasAppKeyExp) || ($expires >= $dtnow->getTimestamp())) {
    //                     return $obCasAppModel->getCasAppKey();
    //                 } else {
    //                     if ($expires < $dtnow->getTimestamp()) {
    //                         array_push($this->messages, $this->message->getMessage(3, 'Message', 'Token expirou.'));
    //                     }
    //                 }
    //             } else {
    //                 array_push($this->messages, $this->message->getMessage(3, 'Message', 'Valor em (app) bloqueado.'));
    //             }
    //         } else {
    //             array_push($this->messages, $this->message->getMessage(3, 'Message', 'Valor em (app) não encontrado.'));
    //         }
    //     } else {
    //         array_push($this->messages, $this->message->getMessage(3, 'Message', 'Informe (app).'));
    //     }

    //     return '';
    // }

    // private static function signatureHash($header_payload, $secret)
    // {
    //     return hash_hmac('sha256', $header_payload, $secret, true);
    // }
}

/**
 * sub: identificador do usuário.
 * iat: o timestamp da emissão do token.
 * key: uma string única, que pode ser usada para validar um token, 
 *      mas vai contra não ter uma autoridade centralizada do emissor.
 * iss: uma string contendo o nome ou identificador do emissor. 
 *      Pode ser um nome de domínio e pode ser usada para descartar tokens de outros aplicativos.
 * nbf: um timestamp de quando o token deve começar a ser considerado válido. 
 *      Deve ser igual ou maior que iat.
 * exp: um timestamp de quando o token deve deixar de ser válido. Deve ser maior que iat e nbf.
 */
