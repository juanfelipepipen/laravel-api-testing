<?php

namespace Pipen\ApiTesting\Exceptions\TemporallyStore;

use Exception;
use JetBrains\PhpStorm\Pure;

class AccountNotFoundException extends Exception
{
    /*
     * Class constructor
     */
    #[Pure]public function __construct()
    {
    parent::__construct('Account not found');
}
}
