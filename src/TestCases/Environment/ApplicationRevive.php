<?php
/** @noinspection PhpUnused */

namespace Pipen\ApiTesting\TestCases\Environment;

use Illuminate\Support\Facades\Artisan;
use Pipen\ApiTesting\Base\ApiTestBase;

class ApplicationRevive extends ApiTestBase
{

    /**
     * Up application
     *
     * @return void
     */
    public function test_up_application(): void
    {
        Artisan::call('up');
    }
}
