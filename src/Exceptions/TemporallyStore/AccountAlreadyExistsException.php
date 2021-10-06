<?php

namespace Pipen\ApiTesting\Exceptions\TemporallyStore;

use Exception;
use JetBrains\PhpStorm\Pure;

class AccountAlreadyExistsException extends Exception
{
    /*
     * Class constructor
     */
    #[Pure]public function __construct(string $key = 'undefined')
    {
    parent::__construct('Account ' . $key . ' already exists');
}
}
