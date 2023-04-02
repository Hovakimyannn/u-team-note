<?php

namespace App\Services\HttpService;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cookie;
use stdClass;

class HttpService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client($this->prepareHeader());
    }

    /**
     * @return array
     */
    private function prepareHeader() : array
    {
        $cookie = Cookie::get(config('session.cookie'));

        return [
            'headers' => [
                'Cookie'       => "laravel_session={$cookie}",
                'Accept'       => 'application/json',
                'Content-type' => 'application/json',
            ]
        ];
    }

    /**
     * @param $url
     *
     * @return stdClass|array|null
     *
     * @throws GuzzleException
     */
    public function get($url) : stdClass|array|null
    {
        try {
            $response = $this->client->get($url);

            return json_decode($response->getBody());
        } catch (Exception $e) {
            throw new Exception();
        }
    }
}
