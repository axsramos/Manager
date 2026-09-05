<?php

namespace App\Controllers\Auth;

use App\Class\Auth\AccountActivationService;
use App\Class\Auth\RegisterClass;
use App\Class\Auth\RegisterEmailVerificationService;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormDesign;
use App\Core\AuthSession;
use App\Core\Controller;
use App\Core\ServiceMail;
use App\Shared\MessageDictionary;

class Register extends Controller
{
    private array $formDesign;
    private array $dataEntry = [];
    private const FIELDS = ['inputFirstName', 'inputLastName', 'inputEmail', 'inputVerificationCode', 'inputPassword', 'inputPasswordConfirm'];

    public function __construct()
    {
        $this->message = new MessageDictionary();
        $this->formDesign = FormDesign::secMessage();
    }

    public function index(): void
    {
        if (isset($_GET['restart'])) {
            AuthSession::set('REGISTER_PENDING', null);
        }

        $this->dataEntry = FormData::secFields(self::FIELDS, $this->getDataInput(self::FIELDS));
        $pending = AuthSession::get()['REGISTER_PENDING'] ?? null;
        $emailPending = is_array($pending) && !empty($pending['email']);
        $showToken = $emailPending && empty($pending['email_verified']);
        $showDetails = $emailPending && !empty($pending['email_verified']);

        if (isset($_POST['btnContinue'])) {
            $verification = (new RegisterEmailVerificationService())->request($this->dataEntry['inputEmail']);
            $this->formDesign['Message'] = $this->activationMessage($verification);

            if ($verification['success']) {
                $pending = [
                    'email' => strtolower(trim($this->dataEntry['inputEmail'])),
                    'requested_at' => date('Y-m-d H:i:s'),
                    'expires_at' => $verification['expires_at'],
                    'email_verified' => false,
                ];
                AuthSession::set('REGISTER_PENDING', $pending);
                $showToken = true;
                $showDetails = false;
            }
        }

        if (isset($_POST['btnVerify'])) {
            if (!is_array($pending) || empty($pending['email'])) {
                $this->formDesign['Message'] = $this->message->getMessage(613);
                $showToken = false;
                $showDetails = false;
            } else {
                $verification = (new RegisterEmailVerificationService())->verify(
                    $pending['email'],
                    $this->dataEntry['inputVerificationCode']
                );
                $this->formDesign['Message'] = $this->activationMessage($verification);

                if ($verification['success']) {
                    $pending['email_verified'] = true;
                    $pending['verified_at'] = date('Y-m-d H:i:s');
                    AuthSession::set('REGISTER_PENDING', $pending);
                    $showToken = false;
                    $showDetails = true;
                } else {
                    $showToken = true;
                    $showDetails = false;
                }
            }
        }

        if (isset($_POST['btnConfirm'])) {
            if (!is_array($pending) || empty($pending['email']) || empty($pending['email_verified'])) {
                $this->formDesign['Message'] = $this->message->getMessage(613);
                $showToken = $emailPending;
                $showDetails = false;
            } else {
                $this->dataEntry['inputEmail'] = $pending['email'];
                $register = new RegisterClass();
                $passwordResult = $register->validatePasswordRule($this->dataEntry['inputPassword']);

                if ($passwordResult['Code'] !== 0 || $passwordResult['Type'] !== 'SUCCESS') {
                    $this->formDesign['Message'] = $passwordResult;
                } else {
                    $result = $register->createAccount([
                        'FirstName' => $this->dataEntry['inputFirstName'],
                        'LastName' => $this->dataEntry['inputLastName'],
                        'Account' => $pending['email'],
                        'Password' => $this->dataEntry['inputPassword'],
                        'PasswordConfirm' => $this->dataEntry['inputPasswordConfirm'],
                    ]);
                    $this->formDesign['Message'] = $result;

                    if ($result['Code'] === 0 && $result['Type'] === 'SUCCESS') {
                        AuthSession::set('REGISTER_PENDING', null);
                        (new ServiceMail())->sendMailRegister($this->dataEntry['inputFirstName'], $pending['email']);
                        header('Location: /Home');
                        return;
                    }
                }
            }
        }

        if ($showToken) {
            $this->dataEntry['inputEmail'] = $pending['email'];
            $this->view('SBAdmin/RegisterTokenView', [
                'FormDesign' => $this->formDesign,
                'FormData' => FormData::secFields(self::FIELDS, $this->dataEntry),
            ]);
            return;
        }

        if ($showDetails) {
            $this->dataEntry['inputEmail'] = $pending['email'];
            $this->view('SBAdmin/RegisterView', [
                'FormDesign' => $this->formDesign,
                'FormData' => FormData::secFields(self::FIELDS, $this->dataEntry),
            ]);
            return;
        }

        $this->view('SBAdmin/RegisterEmailView', [
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields(self::FIELDS, $this->dataEntry),
        ]);
    }

    public function activate(string $token = ''): void
    {
        $result = (new AccountActivationService())->activate(rawurldecode($token));
        $this->view('SBAdmin/ActivationView', ['Activation' => $result]);
    }

    private function activationMessage(array $result): array
    {
        return [
            'Code' => $result['success'] ? 0 : 1,
            'Type' => $result['success'] ? 'SUCCESS' : 'WARNING',
            'Title' => '',
            'Description' => $result['message'],
        ];
    }
}
