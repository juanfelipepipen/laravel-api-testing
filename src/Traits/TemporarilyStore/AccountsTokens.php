<?php

namespace Pipen\ApiTesting\Traits\TemporarilyStore;

use Pipen\ApiTesting\Exceptions\TemporallyStore\AccountAlreadyExistsException;
use Pipen\ApiTesting\Exceptions\TemporallyStore\AccountNotFoundException;

trait AccountsTokens
{
    /**
     * Save account data in session storage
     *
     * @param string $key
     * @param int    $id
     * @param string $username
     * @param string $password
     * @param string $bearerToken
     * @param bool   $throwIfExists
     *
     * @return void
     * @throws \Pipen\ApiTesting\Exceptions\TemporallyStore\AccountAlreadyExistsException
     */
    protected function setAccount(string $key, int $id, string $username = '', string $password = '', string $bearerToken = '', bool $throwIfExists = true)
    {
        # [Throw] AccountAlreadyExistsException
        if (session($key) && $throwIfExists) {
            throw new AccountAlreadyExistsException($key);
        }

        # Storage data
        session([$key => [
            'id'           => $id,
            'username'     => $username,
            'password'     => $password,
            'access_token' => $bearerToken
        ]]);
    }

    /**
     * Get account data
     *
     * @param string $key
     *
     * @return object
     * @throws \Pipen\ApiTesting\Exceptions\TemporallyStore\AccountNotFoundException
     */
    protected function getAccount(string $key): object
    {
        $account = session($key) ?? throw new AccountNotFoundException();

        return (object) $account;
    }

    /**
     * Get id at storage account credentials in session
     *
     * @param string $accountName
     *
     * @return int
     * @throws \Pipen\ApiTesting\Exceptions\TemporallyStore\AccountNotFoundException
     */
    public function getAccountId(string $accountName): int
    {
        return $this->getAccount($accountName)->id;
    }

    /**
     * Return if account token exists
     *
     * @param string $accountName
     *
     * @return bool
     */
    public function existsAccount(string $accountName): bool
    {
        return session($accountName) === null;
    }

    /**
     * Update account params
     *
     * @param string      $key
     * @param string|null $username
     * @param string|null $password
     * @param string|null $bearerToken
     *
     * @return void
     * @throws \Pipen\ApiTesting\Exceptions\TemporallyStore\AccountNotFoundException
     * @throws \Pipen\ApiTesting\Exceptions\TemporallyStore\AccountAlreadyExistsException
     */
    protected function updateAccount(string $key, string $username = null, string $password = null, string $bearerToken = null)
    {
        $data = $this->getAccount($key);

        $newUsername    = $username ?? $data->username;
        $newPassword    = $password ?? $data->password;
        $newAccessToken = $bearerToken ?? $data->access_token;

        $this->setAccount($key, $data->id, $newUsername, $newPassword, $newAccessToken, false);
    }
}
