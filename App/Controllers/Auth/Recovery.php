<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Class\Auth\RecoveryClass;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Core\ServiceMail;
use App\Shared\MessageDictionary;

class Recovery extends Controller
{
  private $formDesign;
  private $dataEntry;
  private const FIELDS = ['inputEmail', 'inputToken', 'inputPassword', 'inputPasswordConfirm'];

  public function __construct()
  {
    $this->message = new MessageDictionary();
    $this->formDesign = FormDesign::secMessage();
  }

  public function index()
  {
    $this->dataEntry = $this->getDataInput($this::FIELDS);

    $redirectTokenView = false;
    $redirectLogin = false;
    $result = $this->message->getMessage(0);

    $recovery = new RecoveryClass();
    $recovery->logout();

    /**
     * Step One
     */
    if (isset($_POST['btnConfirm'])) {
      $result = $recovery->generateTokenToRecoverPassword($this->dataEntry['inputEmail']);

      if ($result['Code'] == 0) {
        $serviceMail = new ServiceMail();
        if ($serviceMail->sendMailRecovery($recovery->name, $this->dataEntry['inputEmail'], $recovery->token)) {
          $result = $this->message->getMessage(611);
          $redirectTokenView = true;
        } else {
          $result = $this->message->getMessage(612);
        }
      }
    }

    /**
     * Step Two
     */
    if (isset($_POST['btnConfirmToken'])) {
      $redirectTokenView = true;
      if (empty($this->dataEntry['inputEmail'])) {
        $result = $this->message->getMessage(613);
      } else {
        if (empty($this->dataEntry['inputToken'] || empty($this->dataEntry['inputPassword']))) {
          $result = $this->message->getMessage(604);
        } else {
          $recovery = new RecoveryClass();
          $result = $recovery->validatePasswordRule($this->dataEntry['inputPassword']);
          
          if ($result['Code'] == 0 && $result['Type'] == 'SUCCESS') {
            $result = $recovery->updatePassword($this->dataEntry['inputToken'], $this->dataEntry['inputEmail'], $this->dataEntry['inputPassword'], $this->dataEntry['inputPasswordConfirm']);
            
            if ($result['Code'] == 0) {
              $redirectLogin = true;
            }
          }
        }
      }
    }

    $this->formDesign['Message'] = $result;

    $data = array(
      'FormDesign' => $this->formDesign,
      'FormData' => FormData::secFields($this::FIELDS, $this->dataEntry),
    );

    if ($redirectLogin) {
      header("Location: /Auth/Login");
    }

    if ($redirectTokenView) {
      $this->view('SBAdmin/TokenView', $data);
    } else {
      $this->view('SBAdmin/RecoveryView', $data);
    }
  }
}
