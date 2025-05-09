<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('workspace_id');
            $table->bigInteger('user_id');
            $table->bigInteger('project_id');
            $table->bigInteger('related_id');
            $table->text('type',20);
            $table->text('data');
            $table->tinyInteger('is_read');
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
