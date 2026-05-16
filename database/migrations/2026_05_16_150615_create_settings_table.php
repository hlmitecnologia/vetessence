<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'github_token', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'github_repo', 'value' => 'hectordufau/vetessence', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'github_branch', 'value' => 'main', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
