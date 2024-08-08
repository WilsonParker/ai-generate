<?php

namespace App\Exceptions\OpentAI;

use Exception;

class NotFoundOpenAIStackException extends Exception
{
    protected $message = 'OpenAI key stack not found';

}
