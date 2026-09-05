<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Class\Pattern\FormDesign;

class LogSystem extends Controller
{
    private $messages = array();
    private $formDesign;

    public function __construct()
    {
        $this->validateAccess('LogSystem');
        // $this->setProgramParameters('LogSystem', '');

        $this->message = new MessageDictionary();


        $this->formDesign = FormDesign::withSideBarAdmin('Controle de Acesso', 'Log do Sistema', 'Log > Do Sistema', $this->getUserMenu(), $this->getSideMenu());
    }

    public function index()
    {
        /**
         * Form Design
         */
        $this->formDesignOnForm('dsp', 0, 'LogSystemViewForm.php', []);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign
        );

        $this->view('SBAdmin/Manager/LogSystemView', $data);
    }

    private function formatDataOnForm() {}

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/LogSystem';

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
