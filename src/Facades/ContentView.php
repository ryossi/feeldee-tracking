<?php

namespace Feeldee\Tracking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Feeldee\Framework\Facades\ContentView
 *
 * @method static ContentViewHistory|false regist(Content $content)
 * @method static int count(Profile|Content $target, string|Carbon $viewed_date = 'all', string $content_type = 'all')
 */
class ContentView extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feeldee\Tracking\Services\ContentViewService::class;
    }
}
