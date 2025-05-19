<?php

namespace Feeldee\Tracking\Models;

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
    protected $fillable = ['viewed_at'];

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
