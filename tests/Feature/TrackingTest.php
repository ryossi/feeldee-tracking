<?php

namespace Tests\Feature;

use Feeldee\Tracking\Services\TrackingService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * HTTPフィルタリング
     * 
     * - TrackingRequestsミドルウェアが既に"tracking"エイリアスで登録済みであることを確認します。
     * - 任意のURLへのリクエストに対してトラッキングを有効にすることができることを確認します。
     * - トラッキングが有効なURLへユーザがアクセスした場合には、自動的にトラックが登録されることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/トラッキング#HTTPフィルタリング
     */
    public function test_tracking_http_filter()
    {
        // 準備
        $ipAddress = '123.456.789.0';
        $userAgent = 'TrackingTest';

        // 実行
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => $ipAddress,
        ])->withHeaders([
            'User-Agent' => $userAgent,
        ])->get('/tracking');

        // 評価
        $response->assertStatus(200)->assertJson(['status' => 'ok']);
        $this->assertDatabaseCount('tracks', 1);
        $this->assertDatabaseHas('tracks', [
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * HTTPフィルタリング
     * 
     * - コンフィグレーションでトラッキングを無効にすることができることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/トラッキング#コンフィグレーション
     */
    public function test_tracking_config_disabled()
    {
        // 準備
        Config::set('tracking.enable', false);

        // 実行
        $response = $this->get('/tracking');

        // 評価
        $response->assertStatus(200)->assertJson(['status' => 'ok']);
        $this->assertDatabaseCount('tracks', 0);
    }

    /**
     * HTTPフィルタリング
     * 
     * - コンフィグレーションでCookieに登録するUIDの有効時間を分単位で設定できることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/トラッキング#コンフィグレーション
     */
    public function test_tracking_config_lifetime()
    {
        // 準備
        $lifetime = 60;
        Config::set('tracking.lifetime', $lifetime);

        // 実行
        $response = $this->get('/tracking');

        // 評価
        $response->assertStatus(200)->assertJson(['status' => 'ok']);
        $this->assertDatabaseCount('tracks', 1);
        $cookies = $response->headers->getCookies();
        $this->assertEquals(1, count($cookies));
        $cookie = $cookies[0];
        $this->assertEquals(TrackingService::SESSION_KEY, $cookie->getName());
        $this->assertEquals($lifetime * 60, $cookie->getMaxAge());
    }
}
