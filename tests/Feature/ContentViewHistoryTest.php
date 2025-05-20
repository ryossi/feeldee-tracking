<?php

namespace Tests\Feature;

use Feeldee\Framework\Models\Photo;
use Feeldee\Framework\Models\Post;
use Feeldee\Framework\Models\Profile;
use Feeldee\Tracking\Facades\ContentView;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ContentViewHistoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * HTTPフィルタリング
     * 
     * - 任意のURLへのリクエストに対してコンテンツ閲覧履歴を有効にすることができることを確認します。
     * - ContentViewRequestsミドルウェアが既に"history.content_view"エイリアスで登録済みであることを確認します。
     * - URLへユーザがアクセスしてコンテンツを閲覧した場合には、自動的にコンテンツ閲覧履歴が登録されることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/コンテンツ閲覧履歴#HTTPフィルタリング
     */
    public function test_content_view_http_filter()
    {
        // 準備
        Auth::shouldReceive('id')->andReturn(1);
        $profie = Profile::create([
            'nickname' => 'test',
            'email' => 'test@feeldee.com',
            'user_id' => 1,
            'title' => 'Tracking Package',
        ]);
        $post = $profie->posts()->create([
            'post_date' => now(),
            'title' => 'test',
            'body' => 'test',
        ]);

        // 実行
        $response = $this->get("/content_view/{$post->id}");

        // 評価
        $response->assertStatus(200)->assertJson(['status' => 'ok', 'content' => $post->id]);
        $this->assertDatabaseCount('content_view_histories', 1);
        $this->assertDatabaseHas('content_view_histories', [
            'profile_id' => $profie->id,
            'content_id' => $post->id,
            'content_type' => Post::type()
        ]);
    }

    /**
     * HTTPフィルタリング
     * 
     * - コンフィグレーションでトラッキングを無効にすることができることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/コンテンツ閲覧履歴#HTTPフィルタリング
     */
    public function test_tracking_config_disabled()
    {
        // 準備
        Config::set('tracking.content_view_history.enable', false);
        Auth::shouldReceive('id')->andReturn(1);
        $profie = Profile::create([
            'nickname' => 'test',
            'email' => 'test@feeldee.com',
            'user_id' => 1,
            'title' => 'Tracking Package',
        ]);
        $post = $profie->posts()->create([
            'post_date' => now(),
            'title' => 'test',
            'body' => 'test',
        ]);

        // 実行
        $response = $this->get("/content_view/{$post->id}");

        // 評価
        $response->assertStatus(200)->assertJson(['status' => 'ok', 'content' => $post->id]);
        $this->assertDatabaseCount('content_view_histories', 0);
    }

    /**
     * HTTPフィルタリング
     * 
     * - ルートモデルバインディングの名称をコンフィグレーションで変更することができることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/コンテンツ閲覧履歴#HTTPフィルタリング
     */
    public function test_content_view_route_model_binding()
    {
        // 準備
        Config::set('tracking.content_view_history.binding_key', 'photo');
        // 準備
        Auth::shouldReceive('id')->andReturn(1);
        $profie = Profile::create([
            'nickname' => 'test',
            'email' => 'test@feeldee.com',
            'user_id' => 1,
            'title' => 'Tracking Package',
        ]);
        $photo = $profie->photos()->create([
            'src' => 'http://example.com/test.jpg',
            'regist_datetime' => now(),
        ]);

        // 実行
        $response = $this->get("/photos/{$photo->id}");

        // 評価
        $response->assertStatus(200)->assertJson(['status' => 'ok', 'content' => $photo->id]);
        $this->assertDatabaseCount('content_view_histories', 1);
        $this->assertDatabaseHas('content_view_histories', [
            'profile_id' => $profie->id,
            'content_id' => $photo->id,
            'content_type' => Photo::type()
        ]);
    }

    /**
     * ファサード
     * 
     * - ファサードでコンテンツ閲覧履歴を登録することができることを確認します。
     * - 閲覧対象プロフィールが、閲覧対象のコンテンツのコンテンツ所有者プロフィールであることを確認します。
     * - 閲覧対象コンテンツが、閲覧対象のコンテンツであることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/コンテンツ閲覧履歴#ファサード
     */
    public function test_content_view_facade()
    {
        // 準備
        Auth::shouldReceive('id')->andReturn(1);
        $profie = Profile::create([
            'nickname' => 'test',
            'email' => 'test@feeldee.com',
            'user_id' => 1,
            'title' => 'Tracking Package',
        ]);
        $location = $profie->locations()->create([
            'title' => 'test',
            'latitude' => 35.681236,
            'longitude' => 139.767125,
            'zoom' => 10,
        ]);

        // 実行
        $history = ContentView::regist($location);

        // 評価
        $this->assertEquals($profie->id, $history->profile->id, '閲覧対象プロフィールが、閲覧対象のコンテンツのコンテンツ所有者プロフィールであること');
        $this->assertEquals($location->id, $history->content->id, '閲覧対象コンテンツが、閲覧対象のコンテンツであること');
        $this->assertDatabaseCount('content_view_histories', 1);
        $this->assertDatabaseHas('content_view_histories', [
            'profile_id' => $profie->id,
            'content_id' => $location->id,
            'content_type' => $location->type()
        ]);
    }
}
