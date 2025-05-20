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
     * @param Content $content コンテンツ
     * @return ContentViewHistory|false コンテンツ閲覧履歴またはfalse
     */
    public function regist(Content $content): ContentViewHistory|false
    {
        if (config('tracking.content_view_history.enable')) {
            // コンテンツ閲覧履歴を登録
            return ContentViewHistory::create([
                'content' => $content,
                'viewed_at' => Carbon::now(),
            ]);
        }

        return false;
    }
}
