<?php

namespace Pipen\ApiTesting\Base;

use Exception;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Pipen\ApiTesting\Http\HandleRequests;
use Pipen\ApiTesting\Traits\JsonAssert;
use Pipen\ApiTesting\Traits\TemporarilyStore\AccountsTokens;
use Pipen\ApiTesting\Traits\ThrowHttpResponseExceptions;

class HttpClient extends \Symfony\Component\HttpFoundation\Response
{
    use AccountsTokens, JsonAssert, ThrowHttpResponseExceptions;

    /**
     * Http client for requests
     *
     * @var \Illuminate\Http\Client\PendingRequest $request
     */
    public PendingRequest $request;

    /**
     * Use app url
     *
     * @var string $appUrl
     */
    protected string $appUrl = '';

    /**
     * If the request default cookies is set
     *
     * @var bool $cookiesSet
     */
    protected bool $cookiesSet = false;

    /**
     * If the request bearer token is set
     *
     * @var bool $bearerTokenSet
     */
    protected bool $bearerTokenSet = false;

    /**
     * Class constructor
     */
    public function __construct(bool $useAppUrl = false)
    {
        $this->request = new PendingRequest();

        if ($useAppUrl) {
            $this->appUrl = env('APP_URL');
        }
    }

    /**
     * Set secret key for API
     *
     * @param string                       $secret
     * @param \GuzzleHttp\Cookie\SetCookie $cookie
     *
     * @return void
     */
    public function setSecret(string $secret, SetCookie $cookie)
    {
        session(['secret' => [
            'key'    => $secret,
            'cookie' => [
                'Value'  => $cookie->getValue(),
                'Domain' => $cookie->getDomain()
            ]
        ]]);
    }

    /**
     * Return request with default cookies
     *
     * @return void
     */
    public function setRequestDefaultCookies(): void
    {
        $laravelMaintenanceCookie = $this->getSecret();

        if ($laravelMaintenanceCookie != null) {
            $this->request->withCookies([
                'laravel_maintenance' => $laravelMaintenanceCookie->cookie['Value']
            ], $laravelMaintenanceCookie->cookie['Domain']);
        }
    }

    /**
     * Set token in request
     *
     * @param string|null $accountName
     *
     * @throws \Exception
     */
    public function setBearerToken(string $accountName = null)
    {
        if ($accountName == 'test') {
            dd($this->request);
        }
        if ($accountName != null) {
            $bearerToken = $this->getAccount($accountName)->access_token;

            //if (!$this->bearerTokenSet) {
            $this->bearerTokenSet = true;
            $this->request->withToken($bearerToken);
            //}
        }
    }

    /**
     * Return secret for maintenance mode
     *
     * @return ?object
     */
    public function getSecret(): ?object
    {
        return session('secret') != null ?
            (object) session('secret') :
            null;
    }

    /**
     * Get uri with app url
     *
     * @param string $uri
     *
     * @return string
     */
    private function getUri(string $uri): string
    {
        return $uri;
    }

    /**
     * Create empty request
     *
     * @return void
     */
    public function prepareRequest(): void
    {
        $this->request = new PendingRequest();
    }

    /**
     * Send GET request
     *
     * @param string            $url
     * @param array|string|null $query
     * @param bool              $successOrFail
     * @param bool              $successOnBool
     * @param string|null       $clientToken
     * @param int|null          $assertCode
     * @param bool              $assertOrFail
     * @param string|null       $assertKey
     * @param int               $assertKeySize
     *
     * @return \Illuminate\Http\Client\Response|bool
     * @throws \Exception
     */
    public function get(string $url, array|string|null $query = null, bool $successOrFail = false, bool $successOnBool = false, string $clientToken = null, int $assertCode = null, bool $assertOrFail = false, string $assertKey = null, int $assertKeySize = -1): Response|bool
    {
        $this->prepareRequest();
        $this->setRequestDefaultCookies();
        $this->setBearerToken($clientToken);

        $response = $this->request->get(
            $url,
            $query
        );

        return $this->generateResponseResult($response, $successOrFail, $successOnBool, $assertCode, $assertOrFail, $assertKey, $assertKeySize);
    }

    /**
     * Send POST request
     *
     * @param string      $url
     * @param array       $data
     * @param bool        $successOrFail
     * @param bool        $successOnBool
     * @param string|null $clientToken
     * @param int|null    $assertCode
     * @param bool        $assertOrFail
     * @param string|null $assertKey
     * @param int         $assertKeySize
     *
     * @return \Illuminate\Http\Client\Response|bool
     * @throws \Exception
     */
    public function post(string $url, array $data = [], bool $successOrFail = false, bool $successOnBool = false, string $clientToken = null, int $assertCode = null, bool $assertOrFail = false, string $assertKey = null, int $assertKeySize = -1): Response|bool
    {
        $this->prepareRequest();
        $this->setRequestDefaultCookies();
        $this->setBearerToken($clientToken);

        $response = $this->request->post(
            $this->getUri($url),
            $data
        );

        return $this->generateResponseResult($response, $successOrFail, $successOnBool, $assertCode, $assertOrFail, $assertKey, $assertKeySize);
    }

    /**
     * Send PUT request
     *
     * @param string      $url
     * @param array       $data
     * @param bool        $successOrFail
     * @param bool        $successOnBool
     * @param string|null $clientToken
     * @param int|null    $assertCode
     * @param bool        $assertOrFail
     * @param string|null $assertKey
     * @param int         $assertKeySize
     *
     * @return \Illuminate\Http\Client\Response|bool
     * @throws \Exception
     */
    public function put(string $url, array $data = [], bool $successOrFail = false, bool $successOnBool = false, string $clientToken = null, int $assertCode = null, bool $assertOrFail = false, string $assertKey = null, int $assertKeySize = -1): Response|bool
    {
        $this->prepareRequest();
        $this->setRequestDefaultCookies();
        $this->setBearerToken($clientToken);

        $response = $this->request->put(
            $this->getUri($url),
            $data
        );

        return $this->generateResponseResult($response, $successOrFail, $successOnBool, $assertCode, $assertOrFail, $assertKey, $assertKeySize);
    }

    /**
     * Send DELETE request
     *
     * @param string      $url
     * @param array       $data
     * @param bool        $successOrFail
     * @param bool        $successOnBool
     * @param string|null $clientToken
     * @param int|null    $assertCode
     * @param bool        $assertOrFail
     * @param string|null $assertKey
     * @param int         $assertKeySize
     *
     * @return \Illuminate\Http\Client\Response|bool
     * @throws \Exception
     */
    public function delete(string $url, array $data = [], bool $successOrFail = false, bool $successOnBool = false, string $clientToken = null, int $assertCode = null, bool $assertOrFail = false, string $assertKey = null, int $assertKeySize = -1): Response|bool
    {
        $this->prepareRequest();
        $this->setRequestDefaultCookies();
        $this->setBearerToken($clientToken);

        $response = $this->request->delete(
            $this->getUri($url),
            $data
        );

        return $this->generateResponseResult($response, $successOrFail, $successOnBool, $assertCode, $assertOrFail, $assertKey, $assertKeySize);
    }

    /**
     * Generate response from http request
     *
     * @param \Illuminate\Http\Client\Response $response
     * @param bool                             $successOrFail
     * @param bool                             $successOnBool
     * @param int|null                         $assertCode
     * @param bool                             $assertOrFail
     * @param string|null                      $assertKey
     * @param int                              $assertKeySize
     *
     * @return bool|mixed
     * @throws \Exception
     */
    private function generateResponseResult(Response $response, bool $successOrFail = false, bool $successOnBool = false, int $assertCode = null, bool $assertOrFail = false, string $assertKey = null, int $assertKeySize = -1): Response|bool
    {
        $bandNotAsserted  = $assertOrFail && $assertCode != $response->status();
        $bandUnsuccessful = $successOrFail && !$response->successful();

        # Throw if request fail and successOnFail
        if ($bandUnsuccessful || $bandNotAsserted) {
            $exceptionMessage = 'Http requests failed, code received: ' . $response->status();

            if ($bandNotAsserted) {
                $exceptionMessage .= ', expected code: ' . $assertCode;
            }

            throw new Exception($exceptionMessage);
        }

        # Assert keys and size response
        if ($assertKey != null) {
            $this->assertJsonKey($response->json(), $assertKey, $assertKeySize);
        }

        # Return if success on bool
        if ($successOnBool) {
            return $response->successful();
        }

        # Return if assert code value is required
        if ($assertCode != null) {
            return $assertCode == $response->status();
        }

        return $response;
    }

    /**
     * Generate a new HandleRequest object for manipulate http requests
     *
     * @param string $uri
     * @param array  $params
     *
     * @return HandleRequests
     */
    public function request(string $uri = '', array $params = []): HandleRequests
    {
        return new HandleRequests($uri, $params);
    }
}