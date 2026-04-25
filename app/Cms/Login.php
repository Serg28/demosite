<?php

namespace App\Cms;

use Vis\Builder\Setting\Login as LoginBase;

class Login extends LoginBase
{
    protected $backgroundUrl = '/cms_files/vis-admin-lock.jpg?2';

    public function onLogin()
    {
        return redirect('/admin/dashboard');
    }
}
