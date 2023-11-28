<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->integer('sports_discipline_id');
            $table->integer('creator_member_id');
            $table->integer('creator_club_id');
            $table->integer('recipient_member_id')->nullable();
            $table->date('match_date');
            $table->time('match_time');
            $table->integer('duration_minutes');
            $table->string('venue');
            $table->integer('coin');
            $table->string('description')->nullable();
            $table->integer('type');
            $table->integer('status');
            $table->integer('team_one')->nullable();
            $table->integer('team_two')->nullable();
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
        Schema::dropIfExists('matches');
    }
}
