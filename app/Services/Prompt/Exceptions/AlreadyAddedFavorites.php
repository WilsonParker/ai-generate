<?php

namespace App\Services\Prompt\Exceptions;

use Exception;

class AlreadyAddedFavorites extends Exception
{
    protected $message = 'Already added to favorites';

}