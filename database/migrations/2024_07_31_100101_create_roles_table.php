<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // This creates an 'id' column as an auto-incrementing integer
            $table->string('name')->unique(); // Ensure there's a unique 'name' column
            $table->timestamps();
        });
        
        DB::table('roles')->insert([
            ['name' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'subadmin', 'created_at' => now(), 'updated_at' => now()]
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
