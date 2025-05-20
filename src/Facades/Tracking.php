<?php

namespace Feeldee\Tracking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Feeldee\Tracking\Facades\Tracking
 *
 * @method static void start()
 * @method static string|null uid()
 */
class Tracking extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feeldee\Tracking\Services\TrackingService::class;
    }
}
