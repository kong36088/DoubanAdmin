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
            $table->string('search', 250);
            $table->integer('user_id');
            $table->string('value', 250);
            $table->timestamp("create_time")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->primary(['search', 'user_id']);
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
