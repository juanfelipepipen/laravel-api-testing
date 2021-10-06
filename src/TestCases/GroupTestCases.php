<?php

namespace Pipen\ApiTesting\TestCases;

class GroupTestCases
{
    /**
     * Tests cases objects
     *
     * @var array $testsCases
     */
    public array $testsCases = array();

    /**
     * Handle setup for lists at tests, in order of execution
     *
     * @return void
     */
    public function handle(): void
    {
        $this->testsCases = [];
    }
}