<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->integer('guest_id')->unsigned();
            $table->string('package_name');
            $table->string('package_image', 2048)->nullable();
            $table->string('package_type');
            $table->string('package_description');
            $table->string('package_inclusion');
            $table->decimal('package_price', 8, 2);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('flag', ['0', '1'])->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
