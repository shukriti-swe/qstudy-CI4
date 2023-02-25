<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class LogoutController extends BaseController
{
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('/'));
    }
}
