<?php

namespace Feeldee\Tracking\Http\Middleware;

use Closure;
use Feeldee\Framework\Models\Content;
use Feeldee\Tracking\Facades\ContentView;
use Illuminate\Http\Request;

class ContentViewRequests
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $binding_key = config('tracking.content_view_history.binding_key');
        if ($request->route()->hasParameter($binding_key)) {
            $content = $request->route($binding_key);
            if ($content instanceof Content) {
                // コンテンツ閲覧履歴登録
                ContentView::regist($content);
            }
        }

        return $response;
    }
}
