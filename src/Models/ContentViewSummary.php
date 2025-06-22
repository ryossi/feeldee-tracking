<?php

namespace Feeldee\Tracking\Models;

use Carbon\Carbon;
use Feeldee\Framework\Models\Content;
use Feeldee\Framework\Models\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * コンテンツ閲覧集計をあらわすモデル
 * 
 */
class ContentViewSummary extends Model
{
    use HasFactory;

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = ['profile_id', 'content_type', 'content_id', 'viewed_date', 'view_count'];

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

    /**
     * プロフィールによる絞り込み条件のスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Profile $profile プロフィール
     * @return void
     */
    public function scopeWhereProfile($query, Profile $profile): void
    {
        $query->where('profile_id', $profile->id);
    }

    /**
     * コンテンツによる絞り込み条件のスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Content $content コンテンツ
     * @return void
     */
    public function scopeWhereContent($query, Content $content): void
    {
        $query->where('content_id', $content->id)
            ->where('content_type', $content->getMorphClass());
    }

    /**
     * コンテンツ種別による絞り込み条件のスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $content_type コンテンツ種別
     * @return void
     */
    public function scopeWhereContentType($query, string $content_type): void
    {
        $query->where('content_type', $content_type);
    }

    /**
     * コンテンツ閲覧日による絞り込み条件のスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|Carbon $viewed_date コンテンツ閲覧日（時刻が含まれる場合は、その日付のみを対象とする）
     * @return void
     */
    public function scopeWhereViewedDate($query, string|Carbon $viewed_date): void
    {
        $query->whereDate('viewed_date', $viewed_date);
    }
}
