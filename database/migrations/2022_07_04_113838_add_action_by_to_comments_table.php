<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionByToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('updater_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();// if admin updated the content of comment
            $table->dateTime('deleted_at')->nullable();
            $table->foreignId('deleter_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
        });
    }
}
