<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Class\Pattern\FormDesign;
use App\Core\Config;
use App\Helpers\DataContentInFiles;

class Terms extends Controller
{
  private $formDesign;
  private $formData;

  public function __construct()
  {
    // (validateAccess) Not required for this Controller //
    // $this->validateAccess(); //

    $this->formDesign = FormDesign::withSideBarAdmin('PRINCIPAL', 'Termos & Condições', 'Terms', $this->getUserMenu(), $this->getSideMenu());
  }

  public function index()
  {
    $this->formData = DataContentInFiles::getDataContent(Config::getPathTerms());

    $data = array(
      "FormDesign" => $this->formDesign,
      'FormData' => $this->formData,
    );

    $this->view('SBAdmin/TermsPrivacyView', $data);
  }
}
