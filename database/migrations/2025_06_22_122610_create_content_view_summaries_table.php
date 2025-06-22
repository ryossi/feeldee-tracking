<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_view_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->comment('閲覧対象プロフィールID')->constrained('profiles')->cascadeOnDelete();
            $table->bigInteger('content_id')->comment('閲覧対象コンテンツID');
            $table->string('content_type')->comment('閲覧対象コンテンツ種別');
            $table->date('viewed_date')->comment('コンテンツ閲覧日');
            $table->integer('view_count')->default(0)->comment('コンテンツ閲覧回数');
            $table->timestamps();

            $table->index(['profile_id']);
            $table->index(['content_id']);
            $table->index(['profile_id', 'content_type']);
            $table->index(['profile_id', 'viewed_date']);
            $table->index(['content_id', 'viewed_date']);
            $table->index(['profile_id', 'content_type', 'viewed_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_view_histories');
    }
};
