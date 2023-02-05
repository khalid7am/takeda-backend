<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatedPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preference_id')->constrained('preferences')->cascadeOnDelete();
            $table->foreignId('related_id')->constrained('preferences')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_preferences');
    }
}
