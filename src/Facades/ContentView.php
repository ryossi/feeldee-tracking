<?php

namespace Feeldee\Tracking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Feeldee\Framework\Facades\ContentView
 *
 * @method static void regist(Content $content)
 */
class ContentView extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feeldee\Tracking\Services\ContentViewService::class;
    }
}
