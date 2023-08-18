<?php

namespace Teamupdivision\SaltId\Core;

use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class SaltIdProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(config('services.saltid.url').'oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return config('services.saltid.url').'oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $userUrl = config('services.saltid.url').'api/v1/me';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->get($userUrl);

        $user = json_decode($response->getBody(), true)['data'];

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'name' => Arr::get($user, 'firstName') . Arr::get($user, 'lastName'),
            'email' => Arr::get($user, 'email'),
            'role' => Arr::get($user, 'role')['name'],
            'company' => Arr::get($user, 'company')['name'],
        ]);
    }
}
