<?php

namespace Pipen\ApiTesting\Base;

use Illuminate\Foundation\Testing\WithFaker;

class ApiTestBase extends HttpClient
{
    use WithFaker;

    /**
     * List of test for not stop
     *
     * @var array $testNotStoppable
     */
    protected array $testNotStoppable = array();

    /**
     * Class constructor
     */
    public function __construct(bool $useAppUrl = false)
    {
        parent::__construct($useAppUrl);
        $this->setUpFaker();
    }

    /**
     * Add test not stoppable
     *
     * @param array $tests
     *
     * @return void
     */
    public function addTestsStoppable(array $tests)
    {
        $this->testNotStoppable = $tests;
    }

    /**
     * Set all tests stoppable
     */
    public function addAllTestsStoppable()
    {
        $this->testNotStoppable = ['*'];
    }

    /**
     * Check if method is not stoppable after handle exception
     *
     * @param string $method
     *
     * @return bool
     */
    public function isStoppable(string $method): bool
    {
        return in_array($method, $this->testNotStoppable) ||
               in_array('*', $this->testNotStoppable);
    }
}