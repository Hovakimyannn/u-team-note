<?php

namespace App\Facades;

use App\Services\HttpService\HttpService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static get(string $string)
 *
 * @see HttpService
 */
class HttpCaller extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'http_caller';
    }
}
