<?php
/** @noinspection ALL */

namespace Pipen\ApiTesting\TestCases\Environment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Pipen\ApiTesting\Base\ApiTestBase;
use Pipen\ApiTesting\Traits\TemporarilyStore\MaintenanceMode;

class ApplicationPrepare extends ApiTestBase
{
    use RefreshDatabase, MaintenanceMode;

    /**
     * Secret ket for down application
     *
     * @var string $secret
     */
    protected string $secret = '';

    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct(true);
        $this->addAllTestsStoppable();
    }

    /**
     * Up application
     *
     * @return void
     */
    public function test_up_aplication(): void
    {
        Artisan::call('up');
    }

    /**
     * Set database for storage information in tests
     *
     * @return void
     */
    public function test_database_config(): void
    {
        if (!(config('database.default')) == config('api-testing.database.connections.test')) {
            throw new \Exception('Database connection is not using test connection');
        }
    }

    /**
     * Down application and set secret key
     *
     * @return void
     */
    public function test_down_aplication(): void
    {
        $this->secret = $this->faker->uuid();

        Artisan::call('down', [
            '--secret' => $this->secret
        ]);
    }

    /**
     * Drop all tables in database
     *
     * @return void
     */
    public function test_database_drop_all_tables_migrate_and_seed()
    {
        Artisan::call('migrate:fresh --seed');
    }

    /**
     * Storage secret cookie for maintenance
     *
     * @return void
     */
    public function test_storage_cookie(): void
    {
        $this->storeMaintenanceModeCookie($this->secret);
    }
}
