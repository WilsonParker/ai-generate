<?php

namespace App\Exceptions\Stock;

use Exception;

class NotEnabledException extends Exception
{
    protected $code = 403;
    protected $message = 'Stock is not enabled';

}
