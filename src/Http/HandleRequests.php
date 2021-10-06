<?php

namespace Pipen\ApiTesting\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Pipen\ApiTesting\Exceptions\HttpClient\HttpRequestRequiredException;
use Pipen\ApiTesting\Exceptions\HttpClient\HttpStatusNotAssertedException;
use Pipen\ApiTesting\Exceptions\HttpClient\UriForRequestRequiredException;
use Pipen\ApiTesting\Traits\TemporarilyStore\AccountsTokens;
use Pipen\ApiTesting\Traits\TemporarilyStore\MaintenanceMode;

class HandleRequests
{
    use AccountsTokens, MaintenanceMode;

    /**
     * Params for sent in the request
     *
     * @var string
     */
    protected string $uri = '';

    /**
     * Params for sent in the request
     *
     * @var array
     */
    protected array $query = [];

    /**
     * if is true throw when assert value is false
     *
     * @var bool
     */
    protected bool $throwAssert = false;

    /**
     * Dump the vars
     *
     * @var bool
     */
    protected bool $dd = false;

    /**
     * Http client for requests
     *
     * @var \Illuminate\Http\Client\PendingRequest $request
     */
    public PendingRequest $request;

    /**
     * Response object from request
     *
     * @var \Illuminate\Http\Client\Response|null
     */
    protected ?Response $response = null;

    /**
     * Class constructor
     *
     * @param string $uri
     * @param array  $query
     * @param bool   $withMaintenanceCookie
     */
    public function __construct(string $uri = '', array $query = [], bool $withMaintenanceCookie = true)
    {
        $this->request = new PendingRequest();

        $this->uri   = $uri;
        $this->query = $query;

        if ($withMaintenanceCookie) {
            $this->withMaintenanceModeSecretKey();
        }
    }

    /**
     * Get query params
     *
     * @return object
     */
    public function getParams(): object
    {
        return (object) $this->query;
    }

    /**
     * Generate a laravel route from named route and params
     *
     * @param string $routeName
     * @param array  $parameters
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     */
    public function route(string $routeName, array $parameters = []): static
    {
        $route     = route($routeName, $parameters);
        $this->uri = $route;

        return $this;
    }

    /**
     * Set a query params for requests
     *
     * @param array $query
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     */
    public function query(array $query = []): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Add bearer token to the headers in the request
     *
     * @param string $accountName
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     * @throws \Pipen\ApiTesting\Exceptions\TemporallyStore\AccountNotFoundException
     */
    public function token(string $accountName): static
    {
        $account = $this->getAccount($accountName);
        $this->request->withToken($account->access_token);

        return $this;
    }

    /**
     * Send GET request
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\UriForRequestRequiredException
     */
    public function get(): static
    {
        $this->authorizeRequests();
        $this->response = $this->request->get(
            $this->uri,
            $this->query
        );

        return $this;
    }

    /**
     * Send POST request
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\UriForRequestRequiredException
     */
    public function post(): static
    {
        $this->authorizeRequests();
        $this->response = $this->request->post(
            $this->uri,
            $this->query
        );

        return $this;
    }

    /**
     * Send PUT request
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\UriForRequestRequiredException
     */
    public function put(): static
    {
        $this->authorizeRequests();
        $this->response = $this->request->put(
            $this->uri,
            $this->query
        );

        return $this;
    }

    /**
     * Send POST request
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\UriForRequestRequiredException
     */
    public function delete(): static
    {
        $this->authorizeRequests();
        $this->response = $this->request->delete(
            $this->uri,
            $this->query
        );

        return $this;
    }

    /**
     * Add maintenance mode cookie to the requests for pass maintenance middleware
     *
     * @return void
     */
    private function withMaintenanceModeSecretKey()
    {
        $laravelMaintenanceCookie = $this->getMaintenanceModeCookie();

        # Add cookie to the request headers
        if ($laravelMaintenanceCookie != null) {
            $this->request->withCookies([
                'laravel_maintenance' => $laravelMaintenanceCookie['cookie']['Value']
            ], $laravelMaintenanceCookie['cookie']['Domain']);
        }
    }

    /**
     * Check if response status code is the excepted
     *
     * @param int $exceptedCode
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     *
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\HttpRequestRequiredException
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\HttpStatusNotAssertedException
     */
    public function assertStatus(int $exceptedCode): static
    {
        $responseStatus = $this->response()->status();
        $asserted       = $responseStatus == $exceptedCode;

        return ($this->throwAssert && !$asserted) ?
            throw new HttpStatusNotAssertedException($exceptedCode, $responseStatus) :
            $this;
    }

    /**
     * Set throw in assert band true
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     */
    public function throw(): static
    {
        $this->throwAssert = true;

        return $this;
    }

    /**
     * Return response object
     *
     * @return \Illuminate\Http\Client\Response|null
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\HttpRequestRequiredException
     */
    public function response(): Response|null
    {
        return $this->response ?? throw new HttpRequestRequiredException();
    }

    /**
     * Returns the body in object array at response
     *
     * @throw
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\HttpRequestRequiredException|\Laravel\Octane\Exceptions\DdException
     */
    public function object(): object
    {
        $this->dumpResponseBody('object');

        return $this->response()->object();
    }

    /**
     * Returns the body at response
     *
     * @throw
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\HttpRequestRequiredException|\Laravel\Octane\Exceptions\DdException
     */
    public function body(): string
    {
        $this->dumpResponseBody('body');

        return $this->response()->body();
    }

    /**
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\HttpRequestRequiredException
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    private function dumpResponseBody(string $type)
    {
        if ($this->dd) {
            $response = $this->response();
            $dump     = $type == 'object' ? $response->object() : $response->body();

            dd($dump);
        }
    }

    /**
     * Check all parameters before send a requests
     *
     * @return void
     * @throws \Pipen\ApiTesting\Exceptions\HttpClient\UriForRequestRequiredException
     */
    public function authorizeRequests(): void
    {
        # [Throw] When uri is empty
        if (empty($this->uri)) {
            throw new UriForRequestRequiredException();
        }
    }

    /**
     * Dump request or response
     *
     * @return \Pipen\ApiTesting\Http\HandleRequests
     */
    public function dd(): static
    {
        $this->dd = true;

        return $this;
    }
}