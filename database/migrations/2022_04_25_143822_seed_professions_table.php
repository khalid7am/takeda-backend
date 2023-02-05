<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedProfessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Profession::create([
            'name' => 'Orvos',
            'code' => 'doctor',
        ]);
        \App\Models\Profession::create([
            'name' => 'Egészségügyi dolgozó',
            'code' => 'nurse',
        ]);
    }
}
