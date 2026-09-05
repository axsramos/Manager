<?php

namespace App\Controllers\Support;

use App\Core\Controller;
use App\Core\Config;
use App\Shared\MessageDictionary;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Core\AuthSession;
use App\Models\CAS\CasAppModel;
use App\Models\CAS\CasRpaModel;
use App\Class\Support\Install;
use App\Models\CAS\CasParModel;

class Package extends Controller
{
  use \App\Traits\DataPackage;

  private $messages = array();
  private $formDesign;
  private $dataEntry;
  private $obCasAppModel;

  public function __construct()
  {
    // (validateAccess) Not required for this controller //
    // $this->validateAccess('Package'); //
    // $this->setProgramParameters('Package', ''); //

    // when it is not necessary to validate access then check if the session has not expired //
    if (!isset(AuthSession::get()['RPS_ID'])) {
      header('Location: /Home/Denied'); // access denied //
      exit();
    }

    // validação para contas administrativas portal siti - [start] //
    // e ADMIN do Adminstrador local.
    if (! in_array(strtoupper(AuthSession::get()['PROFILE']), Config::getTypeUsersRestrictAccess())) {
      header('Location: /Home/Denied'); // access denied //
      exit();
    }
    // validação para contas administrativas portal siti - [end] //


    $this->message = new MessageDictionary();
    $this->obCasAppModel = new CasAppModel();

    $this->formDesign = FormDesign::withSideBarAdmin('SUPORTE MANAGER', 'Package', 'Package', $this->getUserMenu(), $this->getSideMenu());
  }

  public function index()
  {
    $rps_id = AuthSession::get()['RPS_ID'];
    $records = array();

    $fields = CasAppModel::FIELDS;
    $hidden = [];

    $this->dataEntry = $this->getDataInput($fields);
    $this->dataEntry = FormData::secFields($fields, $this->dataEntry);
    $app_id = $this->dataEntry['CasAppCod'];

    if (isset($_POST['btnSearchPackage'])) {
      if (empty($app_id)) {
        array_push($this->messages, $this->message->getMessage(2, 'Message', 'A chave do pacote de instalação é obrigatória.'));
      } else {
        $this->obCasAppModel->setSelectedFields($fields);
        $this->obCasAppModel->CasAppCod = $app_id;

        if ($this->obCasAppModel->readRegister()) {
          $records = $this->obCasAppModel->getRecords()[0];
          $records = $this->dataContract($rps_id, $app_id, $records);
          $records = $this->dataPackage($rps_id, $app_id, $records);
        } else {
          array_push($this->messages, $this->message->getMessage(2, 'Message', 'Produto não encontrado! Verifique o código informado.'));
        }
      }
    }

    if (isset($_POST['btnConfirm'])) {
      $app_id = $_POST['btnConfirm'];
      $this->obCasAppModel->setSelectedFields($fields);
      $this->obCasAppModel->CasAppCod = $app_id;

      if ($this->obCasAppModel->readRegister()) {
        $obInstall = new Install();
        $obInstall->run($this->obCasAppModel->CasAppCod, 'ApplyApplicationSettings');

        // read data package before install //
        $records = $this->obCasAppModel->getRecords()[0];
        $records = $this->dataContract($rps_id, $app_id, $records);
        $records = $this->dataPackage($rps_id, $app_id, $records);
      }
    }

    /**
     * Format Data
     */

    /**
     * Form Design
     */
    $this->formDesignOnForm('dsp', 0, 'PackageViewSearch.php', []);
    $this->formDesign['Fields']['CasAppCod']['TextPlaceholder'] = 'Digite a chave do pacote de instalação';

    /**
     * Show Screen
     */
    $data = array(
      'FormDesign' => $this->formDesign,
      'FormData' => $records
    );

    $this->view('SBAdmin/Support/PackageView', $data);
  }

  private function formDesignOnForm($transMode, $current, $file, $keys)
  {
    $urlKeys = implode('/', $keys);

    $this->formDesign['Tabs']['Current'] = $current;
    $this->formDesign['Tabs']['LoadFile'] = 'App/Views/SBAdmin/Support/' . $file;
    $this->formDesign['Tabs']['Items'][0]['Link'] = '/Support/Package';

    $this->formDesign['Fields'] = CasAppModel::FIELDS_MD;

    if ($this->messages) {
      $this->formDesign['Message'] = $this->messages[0];
    }
  }

  private function dataContract(string $rps_id, string $app_id, array $records): array
  {
    $records['CONTRACT'] = array();
    $records['CONTRACT']['CONTRACTED'] = false;

    $obCasRpaModel = new CasRpaModel();
    $obCasRpaModel->setSelectedFields(['CasRpsCod', 'CasAppCod', 'CasRpaBlq']);
    $obCasRpaModel->CasRpsCod = $rps_id;
    $obCasRpaModel->CasAppCod = $app_id;
    if ($obCasRpaModel->readRegister()) {
      $records['CONTRACT']['CONTRACTED'] = ($obCasRpaModel->CasRpaBlq == 'N' ? true : false);
    }

    // todo: obter detalhes do contrato //

    return $records;
  }

  private function dataPackage(string $rps_id, string $app_id, array $records): array
  {
    $data = $this->getDataPackage($app_id);
    $records['CoverImage'] = '/SBAdmin/images/logo.jpg'; // Default //
    $records['LastUpdated'] = 'Sem informação.';

    if (isset($data)) {
      $records['PACKAGED'] = $data;

      if (isset($data['CoverImage']) && ! empty($data['CoverImage'])) {
        $records['CoverImage'] = $data['CoverImage'];
      }

      if (isset($records['PACKAGED']['Packages'])) {
        foreach ($records['PACKAGED']['Packages'] as $keyPackageItem => $packageItem) {
          // check in table CasPar exists flag //
          $obCasParModel = new CasParModel();
          $obCasParModel->CasRpsCod = $rps_id;
          $task = str_pad($packageItem['Task'], 4, '0', STR_PAD_LEFT);
          $obCasParModel->CasParCod = substr($app_id, 0, 36) . '-TASK-' . $task;
          $records['PACKAGED']['Packages'][$keyPackageItem]['Status'] = 'INDEFINIDO';
          if ($obCasParModel->readRegister()) {
            $records['PACKAGED']['Packages'][$keyPackageItem]['Status'] = 'PROCESSADO';
          }
        }
      }
    }

    $obCasParModel = new CasParModel();
    $obCasParModel->CasRpsCod = $rps_id;
    $obCasParModel->CasParCod = substr($app_id, 0, 36) . '-UPDATED';
    if ($obCasParModel->readRegister()) {
      $dataCasPar = (array) json_decode($obCasParModel->CasParTxt);
      if (isset($dataCasPar['LastUpdated'])) {
        $records['LastUpdated'] = FormatData::transformData('DateTimePt', $dataCasPar['LastUpdated']);
      }
    }

    return $records;
  }
}
