<?php

namespace App\Controller\API\V1\Manager;

use App\Core\Controller;

class Home extends Controller
{
    public function index(): void
    {
        $this->view('JsonView', array('Program' => 'Home', 'Description' => 'This is home on API v1.'));
    }
}
