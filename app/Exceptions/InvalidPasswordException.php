<?php

// app/Exceptions/InvalidPasswordException.php

namespace App\Exceptions;

use Exception;

class InvalidPasswordException extends Exception
{
    protected $message = 'Старий пароль неправильний'; // Сообщение по умолчанию

    public function __construct($message = null)
    {
        if ($message) {
            $this->message = __t($message);
        }
        parent::__construct($this->message);
    }
}

