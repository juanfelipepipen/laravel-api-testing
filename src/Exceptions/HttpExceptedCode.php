<?php

namespace Pipen\ApiTesting\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class HttpExceptedCode extends Exception
{
    /*
     * Class constructor
     */
    #[Pure]public function __construct(int $codeReceived, int $codeExcepted)
    {
    $message = "HTTP Code received: $codeReceived is incorrect, excepted code: $codeExcepted";
    parent::__construct($message);
}
}
