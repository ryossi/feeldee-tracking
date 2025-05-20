<?php

use Feeldee\Framework\Models\Photo;
use Feeldee\Framework\Models\Post;
use Illuminate\Support\Facades\Route;

Route::middleware(['tracking', 'queued.cookies', 'bindings'])->group(
    function () {
        Route::get('/tracking', function () {
            return response()->json(['status' => 'ok']);
        });
        Route::middleware('history.content_view')->group(
            function () {
                Route::get('/content_view/{content}', function (Post $content) {
                    return response()->json(['status' => 'ok', 'content' => $content->id]);
                });
                Route::get('/photos/{photo}', function (Photo $photo) {
                    return response()->json(['status' => 'ok', 'content' => $photo->id]);
                });
            }
        );
    }
);
