<?php

namespace Pipen\ApiTesting\Traits;

trait DatabaseConfigs
{
    /**
     * Set database connection for use
     *
     * @param bool|null $setTestConnection
     *
     * @return void
     */
    public function setDatabaseConnection(bool $setTestConnection = null): void
    {
        if ($setTestConnection === null) {
            $setTestConnection = app()->isDownForMaintenance();
        }

        config(['database.default' => $setTestConnection ?
            config('api-testing.database.connections.test') :
            config('api-testing.database.connections.default')
        ]);
    }
}
