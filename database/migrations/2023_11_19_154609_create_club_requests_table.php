<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_requests', function (Blueprint $table) {
            $table->id();
            $table->string('club_name');
            $table->string('club_id')->nullable();
            $table->integer('type')->comment('1: Create, 2: Delete')->default(\App\Models\ClubRequest::TYPE['create']);
            $table->integer('manager_id');
            $table->integer('number_of_members');
            $table->string('description')->nullable();
            $table->integer('status');
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
        Schema::dropIfExists('club_requests');
    }
}
