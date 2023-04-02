<?php

namespace App\Services\Auth;

use App\Entities\User;
use App\Facades\HttpCaller;
use Exception;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class SsoProvider extends EloquentUserProvider
{
    public function __construct(HasherContract $hasher, $model = null)
    {
        parent::__construct($hasher, $model);
    }

    /**
     * Retrieve a user by their unique identifier.
     * Method is not relevant for our authentication mechanism.
     *
     * @param mixed $identifier
     *
     * @return Authenticatable|null
     */
    public function retrieveById($identifier) : ?Authenticatable
    {
        return null;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     * Method is not relevant for our authentication mechanism.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token) : ?Authenticatable
    {
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     * Method is not relevant for our authentication mechanism.
     *
     * @param Authenticatable $user
     * @param string          $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token) : void
    {
    }

    /**
     * Retrieve a user by the given credentials.
     * Method is not relevant for our authentication mechanism.
     *
     * @param array $credentials
     *
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials) : ?Authenticatable
    {
        return null;
    }

    /**
     * Validate a user against the given credentials.
     * Method is not relevant for our authentication mechanism.
     *
     * @param Authenticatable $user
     * @param array           $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials) : bool
    {
        return true;
    }

    /**
     * Retrieve a user by sso gateway.
     *
     * @return Authenticatable|null
     */
    public function retrieveByAuthGateway() : null|Authenticatable
    {
        try {
            $response = HttpCaller::get(env('SSO_URL').'/user');

            if ($response->role == 'admin') {
                return null;
            }

            $user = new User();

            return $user->fromStdClass($response);
        } catch (Exception $e) {
            return null;
        }
    }
}
