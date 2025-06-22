<?php

namespace Feeldee\Tracking\Services;

use Feeldee\Framework\Models\Content;
use Carbon\Carbon;
use Feeldee\Framework\Models\Profile;
use Feeldee\Tracking\Models\ContentViewHistory;
use Feeldee\Tracking\Models\ContentViewSummary;

class ContentViewService
{
    const ALL = 'all';
    const VIEWED_DATE_TODAY = 'today';
    const VIEWED_DATE_YESTERDAY = 'yesterday';

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

    /**
     * コンテンツ閲覧回数合計集計
     * 
     * プロフィール毎またはコンテンツ毎のコンテンツ閲覧回数合計を集計します。
     * 
     * @param Profile|Content $target 集計対象のプロフィールまたはコンテンツ
     * @param string|Carbon $viewed_date コンテンツ閲覧日時絞り込み条件（all:全て|today:今日|yesterday:昨日|指定日時）
     * @param string $content_type コンテンツ種別絞り込み条件（all:全て|コンテンツ種別）
     * @return int コンテンツ閲覧回数合計
     */
    public function count(Profile|Content $target, string|Carbon $viewed_date = Self::ALL, string $content_type = Self::ALL): int
    {
        // 集計対象の型を確認し、クエリビルダを初期化
        if ($target instanceof Profile) {
            $query = ContentViewSummary::whereProfile($target);
        } elseif ($target instanceof Content) {
            $query = ContentViewSummary::whereContent($target);
        } else {
            return 0;
        }
        // コンテンツ閲覧日時絞り込み条件
        if ($viewed_date !== self::ALL) {
            if ($viewed_date === self::VIEWED_DATE_TODAY) {
                $query->whereViewedDate(Carbon::today());
            } elseif ($viewed_date === self::VIEWED_DATE_YESTERDAY) {
                $query->whereViewedDate(Carbon::yesterday());
            } else {
                $query->whereViewedDate($viewed_date);
            }
        }
        // コンテンツ種別絞り込み条件
        if ($content_type !== self::ALL) {
            $query->whereContentType($content_type);
        }
        // 取得したレコードのコンテンツ閲覧回数の合計を計算
        $result = $query->get()
            ->reduce(function ($carry, $item) {
                return $carry + $item->view_count;
            }, 0);
        // 合計閲覧回数を返す
        return $result;
    }
}
