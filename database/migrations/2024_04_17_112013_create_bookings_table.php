<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
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
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_return')->default('0');
            $table->date('return_date')->nullable();
            $table->time('return_time')->nullable();
            $table->boolean('is_book_someone')->default('0');
            $table->string('head_passenger');
            $table->string('total_passenger');
            $table->boolean('have_code')->default('0');
            $table->unsignedBigInteger('discount_card_id');
            $table->foreign('discount_card_id')->references('id')->on('discount_cards')->onDelete('cascade');
            $table->unsignedBigInteger('vehicle_class_id');
            $table->foreign('vehicle_class_id')->references('id')->on('vehicle_classes')->onDelete('cascade');
            $table->boolean('is_active')->default('0');
            $table->enum('status',['pending','accepted','rejected'])->defualt('pending');
            $table->enum('is_deleted', ['Y', 'N'])->default('N');
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
        Schema::dropIfExists('bookings');
    }
}
