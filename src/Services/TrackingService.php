<?php

namespace Feeldee\Tracking\Services;

use Feeldee\Framework\Models\Track;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

class TrackingService
{
    const KEY = 'feeldee_tracking_uid';

    /**
     * トラッキングを開始します。
     */
    public function start(): void
    {
        if (config('tracking.enable')) {
            $track = Track::find(request()->cookie(self::KEY));
            if (!$track) {
                // 追跡情報が存在しない場合

                // 追跡情報を新規作成
                $userAgent = Request::userAgent();
                $ip_address = Request::ip();
                $track = Track::create([
                    'ip_address' => $ip_address,
                    'user_agent' => $userAgent,
                ]);
                Cookie::queue(self::KEY, $track->uid, config('tracking.lifetime'));
            } else {
                if (config('tracking.continuation', false)) {
                    // 追跡自動延長
                    Cookie::queue(self::KEY, $track->uid, config('tracking.lifetime'));
                }
            }

            // セッションにUIDを一時保存
            session()->flash(self::KEY, $track->uid);
        }
    }

    /**
     * UIDを返却します。
     */
    public function uid(): ?string
    {
        return config('tracking.enable') ? session(self::KEY) : null;
    }
}
