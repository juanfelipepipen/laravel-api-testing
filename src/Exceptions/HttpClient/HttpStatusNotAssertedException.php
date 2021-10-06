<?php

namespace Pipen\ApiTesting\Exceptions\HttpClient;

use Exception;
use JetBrains\PhpStorm\Pure;

class HttpStatusNotAssertedException extends Exception
{
    /*
     * Class constructor
     */
    #[Pure]public function __construct(int $codeExcepted, int $codeReceived)
    {
    parent::__construct("Received http $codeReceived code was not what was expected, a $codeExcepted");
}
}
