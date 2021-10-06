<?php

namespace Pipen\ApiTesting\Exceptions\HttpClient;

use Exception;
use JetBrains\PhpStorm\Pure;

class HttpRequestRequiredException extends Exception
{
    /*
     * Class constructor
     */
    #[Pure]public function __construct()
    {
    parent::__construct('It is necessary to start an http request to get a response');
}
}
