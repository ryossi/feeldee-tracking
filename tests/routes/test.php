<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['tracking', 'queued.cookies'])->group(
    function () {
        Route::get('/tracking', function () {
            return response()->json(['status' => 'ok']);
        });
    }
);
