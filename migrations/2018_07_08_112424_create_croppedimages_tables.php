<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('croppedimages', function (Blueprint $table) {
            $table->increments('id');
			$table->morphs('uplink');
			$table->string('filename', 250);
			$table->string('alt', 250)->nullable();
			$table->string('mime', 50);
			$table->integer('orig_w')->unsigned();
			$table->integer('orig_h')->unsigned();
			$table->string('hash', 100);
			$table->tinyInteger('position')->unsigned()->index();
            $table->timestamps();
        });

        Schema::create('croppedimages_crops', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('cropped_image_id')->unsigned()->index();
			$table->string('name', 250);
			$table->float('x', 12, null);
			$table->float('y', 12, null);
			$table->float('scale', 12, null);
			$table->integer('w')->nullable();
			$table->integer('h')->nullable();
			
			$table->foreign('cropped_image_id')->references('id')->on('croppedimages')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_crops');
		Schema::dropIfExists('images');
    }
}
