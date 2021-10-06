<?php

namespace Pipen\ApiTesting\Traits;

use Pipen\ApiTesting\Exceptions\HttpExceptedCode;

trait ThrowHttpResponseExceptions
{
    /**
     * Throw exception when http code received is not the excepted codee
     *
     * @return void
     * @throws \Throwable
     */
    public function throwRequestExceptedCode(int $codeExcepted, int $codeReceived)
    {
        throw_if(
            $codeReceived != $codeExcepted,
            new HttpExceptedCode($codeReceived, $codeExcepted)
        );
    }
}
