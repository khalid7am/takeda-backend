<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->index();
            $table->string('institution')->nullable();
            $table->string('seal_number')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('profession_id')->nullable()->constrained('professions')->nullOnDelete();
            $table->string('profile_picture')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->softDeletes();
        });
    }
}
