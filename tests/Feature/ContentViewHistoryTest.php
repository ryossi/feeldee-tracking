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
     * コンテンツ閲覧集計
     * 
     * - コンテンツ閲覧履歴登録毎に登録されることを確認します。
     * - 一致するコンテンツ閲覧集計が存在しない場合は、新規作成されコンテンツ閲覧回数に1がセットされることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/コンテンツ閲覧履歴#コンテンツ閲覧集計
     */
    public function test_content_view_summary_new()
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
        ContentView::regist($location);

        // 評価
        // ンテンツ閲覧履歴登録毎に登録されること
        $this->assertDatabaseCount('content_view_summaries', 1);
        // 一致するコンテンツ閲覧集計が存在しない場合は、新規作成されコンテンツ閲覧回数に1がセットされること
        $this->assertDatabaseHas('content_view_summaries', [
            'profile_id' => $profie->id,
            'content_id' => $location->id,
            'content_type' => $location->type(),
            'view_count' => 1,
        ]);
    }

    /**
     * コンテンツ閲覧集計
     * 
     * - コンテンツ閲覧履歴登録毎に更新されることを確認します。
     * - 一致するコンテンツ閲覧集計が存在する場合は、コンテンツ閲覧回数が1つカウントアップされることを確認します。
     * 
     * @link https://github.com/ryossi/feeldee-tracking/wiki/コンテンツ閲覧履歴#コンテンツ閲覧集計
     */
    public function test_content_view_summary_update()
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
        ContentView::regist($location);
        $this->assertDatabaseCount('content_view_summaries', 1);
        $this->assertDatabaseHas('content_view_summaries', [
            'profile_id' => $profie->id,
            'content_id' => $location->id,
            'content_type' => $location->type(),
            'view_count' => 1,
        ]);
        ContentView::regist($location);

        // 評価
        // ンテンツ閲覧履歴登録毎に登録されること
        $this->assertDatabaseCount('content_view_summaries', 1);
        // 一致するコンテンツ閲覧集計が存在しない場合は、新規作成されコンテンツ閲覧回数に1がセットされること
        $this->assertDatabaseHas('content_view_summaries', [
            'profile_id' => $profie->id,
            'content_id' => $location->id,
            'content_type' => $location->type(),
            'view_count' => 2,
        ]);
    }

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
     * ファサードによる登録
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
