<?php

namespace Pipen\ApiTesting\Exceptions\HttpClient;

use JetBrains\PhpStorm\Pure;

class UriForRequestRequiredException extends \Exception

{
    /*
     * Class constructor
     */
    #[Pure]public function __construct()
    {
    parent::__construct('The uri where the request will be sent is needed');
}
}
