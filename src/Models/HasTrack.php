<?php

namespace Feeldee\Tracking\Models;

use Feeldee\Tracking\Facades\Tracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasTrack
{
    public static function bootHasTrack()
    {
        static::creating(function (Model $model) {
            $model->uid = Tracking::uid();
        });
    }

    /**
     * Get the phone associated with the user.
     */
    public function track(): HasOne
    {
        return $this->hasOne(Track::class, 'uid', 'uid');
    }
}
