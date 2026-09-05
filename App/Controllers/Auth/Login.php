<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Class\Pattern\FormDesign;
use App\Class\Auth\LoginClass;
use App\Core\AuthSession;
use App\Models\CAS\CasUsrModel;

class Login extends Controller
{
  private $formDesign;
  private $dataEntry;
  private const FIELDS = ['inputEmail', 'inputPassword'];

  public function __construct()
  {
    $this->message = new MessageDictionary();
    $this->formDesign = FormDesign::secMessage();
  }

  public function index()
  {
    $this->dataEntry = $this->getDataInput($this::FIELDS);

    $obLoginClass = new LoginClass();
    $obLoginClass->logout();

    if (isset($_GET['activation']) && $_GET['activation'] === 'required') {
      $this->formDesign['Message'] = $this->message->getMessage(625);
    }

    /**
     * Standard Login
     */
    if (isset($_POST['btnConfirm'])) {
      $result = $obLoginClass->login($this->dataEntry['inputEmail'], $this->dataEntry['inputPassword']);

      $this->formDesign['Message'] = $result;

      if ($result['Code'] == 0 && $result['Type'] == 'SUCCESS') {
        if (isset(AuthSession::get()['HOME_PAGE'])) {
          header("Location: " . AuthSession::get()['HOME_PAGE']);
        } else {
          header("Location: /Home");
        }
      }
    }

    $data = array(
      'FormDesign' => $this->formDesign,
    );

    $this->view('SBAdmin/LoginView', $data);
  }

  public function swap($rps_id)
  {
    $obCasUsrModel = new CasUsrModel();
    $obCasUsrModel->setSelectedFields(['CasUsrCod', 'CasUsrDmn', 'CasUsrLgn', 'CasUsrPwd']);
    $obCasUsrModel->CasUsrCod = AuthSession::get()['USR_ID'];

    if ($obCasUsrModel->readRegister()) {
      $account_password = $obCasUsrModel->getRecords()[0]['CasUsrPwd'];
      $account_email = $obCasUsrModel->getRecords()[0]['CasUsrLgn'] . $obCasUsrModel->getRecords()[0]['CasUsrDmn'];

      $obLoginClass = new LoginClass();
      $result = $obLoginClass->login($account_email, $account_password, $rps_id);

      $linkRedirect = '/Home';

      if ($result['Code'] == 0 && $result['Type'] == 'SUCCESS') {
        if (isset(AuthSession::get()['HOME_PAGE']) && !empty(AuthSession::get()['HOME_PAGE'])) {
          $linkRedirect = AuthSession::get()['HOME_PAGE'];
        }
      }
      
      header('Location: ' . $linkRedirect);
    }
  }
}
