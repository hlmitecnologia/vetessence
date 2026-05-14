<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardingKennelsTable extends Migration
{
    public function up()
    {
        Schema::create('boarding_kennels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('size', 50)->nullable();
            $table->integer('capacity')->default(1);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('boardings', function (Blueprint $table) {
            $table->unsignedBigInteger('kennel_id')->nullable()->after('id');
            $table->foreign('kennel_id')->references('id')->on('boarding_kennels')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('boardings', function (Blueprint $table) {
            $table->dropForeign(['kennel_id']);
            $table->dropColumn('kennel_id');
        });
        Schema::dropIfExists('boarding_kennels');
    }
}
