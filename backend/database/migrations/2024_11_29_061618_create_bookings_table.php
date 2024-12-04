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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->integer('guest_id')->unsigned();
            $table->string('email');
            $table->string('phone');
            $table->string('full_name');
            $table->date('event_date');
            $table->time('event_time');
            $table->integer('guest_count');
            $table->string('venue_name');
            $table->string('special_requests');
            $table->string('payment_method');
            $table->boolean('terms_accepted')->default(0);
            $table->enum('status', ['pending', 'confirmed', 'ongoing', 'preparing', 'completed', 'cancelled'])->default('pending');
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
        Schema::dropIfExists('bookings_tabloe');
    }
};
