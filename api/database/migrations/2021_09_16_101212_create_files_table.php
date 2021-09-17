<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

//            do we need metadata?
//            $table->text('meta')->comment('file metadata');

            $table->string('abs_url',255)->comment('absolute url');
            $table->string('path',255)->comment('path relative to hosting server');
            $table->string('disk',100);

            // do we need timestamps?
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
        Schema::dropIfExists('files');
    }
}
