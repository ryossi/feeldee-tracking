<?php

namespace Feeldee\Tracking\Models;

use Carbon\Carbon;
use Feeldee\Framework\Models\Content;
use Feeldee\Framework\Models\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * コンテンツ閲覧履歴をあらわすモデル
 * 
 */
class ContentViewHistory extends Model
{
    use HasFactory, HasTrack;

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = ['content', 'viewed_at'];

    /**
     * モデルの「起動」メソッド
     */
    protected static function booted(): void
    {
        // コンテンツ閲覧履歴を登録
        static::creating(function ($model) {
            if ($model->content instanceof Content) {
                $model->profile_id = $model->content->profile->id;
                $model->content_type = $model->content->type();
                $model->content_id = $model->content->id;
                unset($model['content']);
            }
        });

        // コンテンツ閲覧集計を登録
        static::created(function ($model) {
            $summary = ContentViewSummary::firstOrNew([
                'profile_id' => $model->profile_id,
                'content_type' => $model->content_type,
                'content_id' => $model->content_id,
                'viewed_date' => $model->viewed_at->format('Y-m-d'),
            ]);
            $summary->view_count = ($summary->view_count ?? 0) + 1;
            $summary->save();
        });
    }

    /**
     * 閲覧対象プロフィール
     *
     * @return BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * 閲覧対象コンテンツ
     */
    public function content()
    {
        return $this->morphTo();
    }
}
