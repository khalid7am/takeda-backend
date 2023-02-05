<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Role::create([
            'name' => "Szuperadmin",
            'code' => "superadmin",
        ]);
        \App\Models\Role::create([
            'name' => "Szerkesztő",
            'code' => "editor",
        ]);
        \App\Models\Role::create([
            'name' => "Felhasználó",
            'code' => "user",
        ]);
    }
}
