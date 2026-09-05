<?php

namespace App\Controllers\Support;

use App\Core\Controller;
use App\Class\Pattern\FormDesign;
use App\Controls\Menu\PanelMenuSupportControls;

class Home extends Controller
{
    private $formDesign;

    public function __construct()
    {
        // (validateAccess) Not required for this controller //
        // $this->validateAccess('Home'); //
        // $this->setProgramParameters('Home', ''); //

        $this->formDesign = FormDesign::withSideBarAdmin('PRINCIPAL', 'Suporte', 'Suporte', $this->getUserMenu(), $this->getSideMenu());
    }

    public function index()
    {
        $data = array(
            "FormDesign" => $this->formDesign,
        );

        $this->view('SBAdmin/Support/HomeView', $data);
    }
}
