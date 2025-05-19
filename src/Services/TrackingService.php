<?php

namespace Feeldee\Tracking\Services;

use Feeldee\Tracking\Models\Track;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

class TrackingService
{
    const SESSION_KEY = 'feeldee_tracking_uid';

    /**
     * トラッキングを開始します。
     */
    public function start(): void
    {
        if (config('tracking.tracking.enable')) {
            $track = Track::find(request()->cookie(self::SESSION_KEY));
            if (!$track) {
                // 追跡情報が存在しない場合

                // 追跡情報を新規作成
                $userAgent = Request::userAgent();
                $ip_address = Request::ip();
                $track = Track::create([
                    'ip_address' => $ip_address,
                    'user_agent' => $userAgent,
                ]);
                Cookie::queue(self::SESSION_KEY, $track->uid, config('tracking.tracking.lifetime'));
            } else {
                if (config('tracking.tracking.continuation', false)) {
                    // 追跡自動延長
                    Cookie::queue(self::SESSION_KEY, $track->uid, config('tracking.tracking.lifetime'));
                }
            }

            // セッションにUIDを一時保存
            session()->flash(self::SESSION_KEY, $track->uid);
        }
    }

    /**
     * UIDを返却します。
     * 
     * @return string|null UUIDまたはnull
     */
    public function uid(): string|null
    {
        return config('tracking.tracking.enable') ? session(self::SESSION_KEY) : null;
    }
}
