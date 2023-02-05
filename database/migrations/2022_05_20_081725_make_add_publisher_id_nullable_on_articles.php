<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeAddPublisherIdNullableOnArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table){
            $table->foreignId('publisher_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
        });
    }
}
