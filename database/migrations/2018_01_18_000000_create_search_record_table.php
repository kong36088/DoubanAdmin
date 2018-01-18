<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_record', function (Blueprint $table) {
            $table->string('primary', 250)->nullable();
            $table->string('secondary', 250)->nullable();
            $table->string('not', 250)->nullable();
            $table->integer('user_id');
            $table->string('value', 250)->nullable();
            $table->timestamp("create_time")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->index(['primary', 'secondary', 'not', 'user_id']);
            $table->index(['value', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_record');
    }
}
