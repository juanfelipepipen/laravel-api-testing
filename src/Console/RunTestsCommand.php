<?php

namespace Pipen\ApiTesting\Console;

use Illuminate\Console\Command;
use Pipen\ApiTesting\HandleTests;

class RunTestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-testing:run
                            {--group= : Group of tests for run}
                            {--exceptions : True if need show exception trace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute tests for internal use';

    /**
     * Code at colors for outputs
     *
     * @var array $colors
     */
    private array $colors = array(
        'red'     => "\e[31m",
        'green'   => "\e[32m",
        'yellow'  => "\e[33m",
        'blue'    => "\e[34m",
        'default' => "\e[39m"
    );

    /**
     * Handle tests
     *
     * @var HandleTests
     */
    protected HandleTests $testsInstance;

    /**
     * Header for test results table
     *
     * @var array $resultsHeaders
     */
    protected array $tableResultsHeaders = array(
        'Test name method',
        'Test group',
        'Execution time (seconds)',
        'Pass'
    );

    /**
     * Header for test errors table
     *
     * @var array $resultsHeaders
     */
    protected array $tableErrorsHeaders = array(
        'Test name method',
        'Test group',
        'Execution time (seconds)',
        'Error info'
    );

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->testsInstance = new HandleTests();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (env('APP_ENV') == 'local') {

            if ($this->option('group') != null) {
                $this->info('Starting internal tests');
                $this->line('');

                # Tests instance
                $this->testsInstance->setTestsCasesGroup($this->option('group'));
                $this->testsInstance->setShowExceptions($this->option('exceptions'));
                $this->testsInstance->setup();

                $numTests = $this->testsInstance->getNumTestsCase();

                # Run tests
                if ($numTests > 0) {
                    $this->output->progressStart($numTests);

                    # Walk test execution
                    while ($this->testsInstance->handleNextTests()) {
                        $this->output->progressAdvance();
                    }

                    // Sleep but is necessary for show progress bar
                    sleep(1);

                    $this->testsInstance->iTestCase == $numTests - 1 ?
                        $this->output->progressFinish() :
                        $this->output->progressAdvance();

                    # Results table
                    $this->output->info('Test result');
                    $this->table($this->tableResultsHeaders, $this->getTestsResultsRows());

                    # Errors table
                    if (count($this->testsInstance->testCasesErrors) > 0) {
                        $this->output->info('Test error');
                        $this->table($this->tableErrorsHeaders, $this->getTestsErrorsRows());
                    } else {
                        $this->output->info('No error detected in any test!');
                    }

                } else {
                    $this->error('No test available to run');
                }
            } else {
                $this->error('Please enter Tests groups --group=');
            }
        } else {
            $this->error("Can't start tests in production environment, only local is allowed");
        }
    }

    /**
     * Get array result at tests for generate table
     *
     * @return array
     */
    public function getTestsResultsRows(): array
    {
        $resultsTable = array();

        foreach ($this->testsInstance->testCasesResults as $testKey => $testGroup) {
            foreach ($testGroup as $test) {
                array_push($resultsTable, array(
                    $test['method'],
                    $testKey,
                    $test['time'],
                    ($test['pass'] ? $this->colors['green'] . '√' : $this->colors['red'] . 'x') .
                    $this->colors['default']
                ));
            }
        }

        return $resultsTable;
    }

    /**
     * Get array errors at tests for generate table
     *
     * @return array
     */
    public function getTestsErrorsRows(): array
    {
        $errorsTable = array();

        foreach ($this->testsInstance->testCasesErrors as $errorKey => $errorGroup) {
            foreach ($errorGroup as $test) {
                array_push($errorsTable, array(
                    $test['method'],
                    $errorKey,
                    $test['time'],
                    $test['message'],
                ));
            }
        }

        return $errorsTable;
    }
}
