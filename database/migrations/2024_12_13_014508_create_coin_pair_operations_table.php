<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinPairOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coin_pair_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coin_pair_id');
            $table->string('bot_operation');
            $table->decimal('upper_threshold', 8, 2)->nullable();
            $table->decimal('lower_threshold', 8, 2)->nullable();
            $table->timestamp('running_time_start')->nullable();
            $table->timestamp('running_time_close')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('coin_pair_id')->references('id')->on('coin_pairs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coin_pair_operations');
    }
}
