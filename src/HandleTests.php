<?php

namespace Pipen\ApiTesting;

use Throwable;
use Pipen\ApiTesting\TestCases\Environment\ApplicationMiddleware;

class HandleTests
{
    /**
     * Test cases groups instance namespace
     *
     * @var string $testsCaseGroupInstance
     */
    protected string $testsCaseGroupInstance = '';

    /**
     * Tests cases group for run
     *
     * @var string $testsCasesGroup
     */
    protected string $testsCasesGroup = '';

    /**
     * Tests cases instance for handle individual tests
     *
     * @var mixed $testsCasesInstances
     */
    protected mixed $testsCasesInstance;

    /**
     * Tests cases instance for handle individual tests
     *
     * @var mixed $testsCasesInstances
     */
    protected mixed $testsCasesInstances;

    /**
     * All method in instances for tests cases
     *
     * @var array $testCasesMethods
     */
    protected array $testCasesMethods = array();

    /**
     * Results after run tests
     *
     * @var array $testCasesResults
     */
    public array $testCasesResults = array();

    /**
     * Errors after execute tests
     *
     * @var array $testCasesErrors
     */
    public array $testCasesErrors = array();

    /**
     * Default test for run tests cases
     *
     * @var array
     */
    protected array $testCasesDefault = array(
        ApplicationMiddleware::class,
    );

    /**
     * Band for show exceptions trace in console output
     *
     * @var bool
     */
    protected bool $configShowExceptionsTrace;

    /**
     * Current index at test case running
     *
     * @var int $iTestCase
     */
    public int $iTestCase = 0;

    /**
     * Set test case type
     *
     * @param string $testsCasesType
     *
     * @return void
     */
    public function setTestsCasesGroup(string $testsCasesType): void
    {
        $this->testsCasesGroup = strtoupper($testsCasesType[0]) . substr($testsCasesType, 1);
    }

    /**
     * Set tests cases group instance for handle tests
     *
     * @return void
     */
    public function setTestsCasesGroupInstance(): void
    {
        $this->testsCaseGroupInstance = '\\App\\Tests\\' . $this->testsCasesGroup . '\\GroupTestCases';
    }

    /**
     * Set config for show exceptions trace in console output
     *
     * @param bool $show
     */
    public function setShowExceptions(bool $show)
    {
        $this->configShowExceptionsTrace = $show;
    }

    /**
     * Get number of tests cases
     *
     * @return int
     */
    public function getNumTestsCase(): int
    {
        return count($this->testCasesMethods);
    }

    /**
     * Find test case group instance for handle list of tests
     *
     * @return bool
     */
    public function findTestsCaseGroup(): bool
    {
        $dirGroupTestCases = app_path('Tests/') . $this->testsCasesGroup;
        $dirTestCases      = app_path('Tests/') . $this->testsCasesGroup . '/TestCases';

        return (is_dir($dirGroupTestCases) && is_dir($dirTestCases));
    }

    /**
     * Save test result
     *
     * @param string $class
     * @param string $method
     * @param float  $executionTime
     * @param bool   $pass
     *
     * @return void
     */
    public function addTestResult(string $class, string $method, float $executionTime, bool $pass): void
    {
        # Create class array results
        if (!isset($this->testCasesResults[$class])) {
            $this->testCasesResults[$class] = array();
        }

        # Save test result
        array_push($this->testCasesResults[$class], array(
            'method' => $method,
            'time'   => $executionTime,
            'pass'   => $pass
        ));
    }

    /**
     * Save test error info
     *
     * @param string $class
     * @param string $method
     * @param float  $executionTime
     * @param string $errorMessage
     *
     * @return void
     */
    public function addTestError(string $class, string $method, float $executionTime, string $errorMessage): void
    {
        # Create class array errors
        if (!isset($this->testCasesErrors[$class])) {
            $this->testCasesErrors[$class] = array();
        }

        # Save test error
        array_push($this->testCasesErrors[$class], array(
            'method'  => $method,
            'time'    => $executionTime,
            'message' => $errorMessage
        ));
    }

    /**
     * Handle tests in order
     *
     * @return bool
     */
    public function handleNextTests(): bool
    {
        $testCaseStoppable = false;
        $testCaseResult    = false;

        if (isset($this->testCasesMethods[$this->iTestCase])) {
            $testTimeStart    = microtime(true);
            $testCase         = (object) $this->testCasesMethods[$this->iTestCase];
            $testCaseInstance = (object) $this->testsCasesInstances[$testCase->class];
            $fnCalculateTime  = fn () => round((microtime(true) - $testTimeStart), 3);

            try {
                $testResult = $testCaseInstance->{$testCase->method}();

                if ($testResult || $testResult === null) {
                    $testCaseResult = true;
                } else {
                    throw new \Exception('Test not passed, unidentified problem');
                }

            } catch (Throwable $th) {

                if (!$this->configShowExceptionsTrace) {
                    $this->addTestError($testCase->class, $testCase->method, $fnCalculateTime(), $th->getMessage());
                    $testCaseStoppable = $testCaseInstance->isStoppable($testCase->method);
                } else {

                    # Output exception trace
                    print(
                        PHP_EOL . PHP_EOL . 'Test group: ' . $testCase->class .
                        PHP_EOL . 'Test method: ' . $testCase->method .
                        PHP_EOL . PHP_EOL . ' --- Exception trace ---' .
                        PHP_EOL . PHP_EOL . $th->getMessage() .
                        PHP_EOL . $th->getLine() .
                        PHP_EOL . $th->getTraceAsString() . PHP_EOL
                    );
                    exit();
                }

            } finally {
                $this->iTestCase++;
            }

            $this->addTestResult($testCase->class, $testCase->method, $fnCalculateTime(), $testCaseResult);
        }

        return !($testCaseStoppable || $this->iTestCase == $this->getNumTestsCase());
    }

    /**
     * Load test case instances and methods
     *
     * @return void
     */
    public function loadTestsCases()
    {
        $this->addDefaultTests();

        foreach ($this->testsCasesInstance->testsCases as $testsCase) {
            $testInstance     = new $testsCase();
            $testInstanceName = get_class($testInstance);
            $testMethods      = get_class_methods($testsCase);

            $this->testsCasesInstances[$testInstanceName] = $testInstance;

            foreach ($testMethods as $method) {
                if (str_starts_with($method, 'test')) {
                    array_push($this->testCasesMethods, [
                            'method' => $method,
                            'class'  => $testInstanceName
                        ]
                    );
                }
            }
        }
    }

    /**
     * Add default tests for run
     *
     * @return void
     */
    protected function addDefaultTests(): void
    {
        foreach ($this->testCasesDefault as $defaultTestCase) {
            array_unshift($this->testsCasesInstance->testsCases, $defaultTestCase);
        }
    }

    /**
     * Run tests
     *
     * @return void
     */
    public function setup(): void
    {
        if ($this->findTestsCaseGroup()) {
            $this->setTestsCasesGroupInstance();
            $this->testsCasesInstance = new $this->testsCaseGroupInstance();
            $this->testsCasesInstance->handle();
            $this->loadTestsCases();
        }
    }
}