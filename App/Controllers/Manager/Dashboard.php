<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Class\Pattern\FormDesign;

class Dashboard extends Controller
{
  private $formDesign;

  public function __construct()
  {
    $this->validateAccess('Dashboard');
    // $this->setProgramParameters('Dashboard', '');
    
    $this->formDesign = FormDesign::withSideBarAdmin('PRINCIPAL', 'Dashboard', 'Dashboard', $this->getUserMenu(), $this->getSideMenu());
    $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
    $this->formDesign['Scripts']['Body'] = ['chart-area-demo', 'chart-bar-demo', 'dataTables'];
  }

  public function index()
  {
    $data = array(
      'FormDesign' => $this->formDesign,
    );

    $this->view('SBAdmin/DashboardView', $data);
  }
}
