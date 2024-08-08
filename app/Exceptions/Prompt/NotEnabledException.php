<?php

namespace App\Exceptions\Prompt;

use Exception;

class NotEnabledException extends Exception
{
    protected $code = 403;
    protected $message = 'Prompt is not enabled';

}
