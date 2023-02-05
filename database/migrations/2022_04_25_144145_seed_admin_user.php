<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\User::create([
            'name' => 'Admin Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('secret'),
            'institution' => 'WebOrigo Zrt.',
            'seal_number' => '123456789',
            'profession_id' => 1,
            'role_id' => 1,
            'accepted_at' => now(),
        ]);
    }
}
