<?php

namespace Feeldee\Tracking\Services;

use Feeldee\Framework\Models\Content;
use Carbon\Carbon;
use Feeldee\Tracking\Models\ContentViewHistory;

class ContentViewService
{
    /**
     * コンテンツ閲覧履歴を登録します。
     * 
     * @return Content $content コンテンツ
     */
    public function regist(Content $content): void
    {
        if (config('tracking.content_view_history.enable')) {
            // コンテンツ閲覧履歴を登録
            ContentViewHistory::create([
                'content' => $content,
                'viewed_at' => Carbon::now(),
            ]);
        }
    }
}
