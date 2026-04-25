<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImageStorageMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vis_images', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('file_folder');
            $table->text('file_source');
            $table->text('file_cms_preview');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->text('exif_data');
            $table->tinyInteger('is_active')->default("1");
            $table->timestamp('date_time_source');
            $table->timestamps();
        });

        Schema::create('vis_galleries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->timestamp('event_date');
            $table->tinyInteger('is_active');
            $table->timestamps();
        });


        Schema::create('vis_images2galleries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('id_image')->unsigned();
            $table->integer('id_gallery')->unsigned();
            $table->tinyInteger('is_preview');
            $table->tinyInteger('priority');

            $table->foreign('id_image')->references('id')->on('vis_images')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_gallery')->references('id')->on('vis_galleries')->onDelete('cascade')->onUpdate('cascade');

        });

        Schema::create('vis_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->tinyInteger('is_active');
            $table->timestamps();
        });

        Schema::create('vis_tags2entities', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('id_tag')->unsigned();
            $table->integer('id_entity')->unsigned();
            $table->string('entity_type', 64);

            $table->foreign('id_tag')->references('id')->on('vis_tags')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('vis_videos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('id_preview')->unsigned()->nullable();
            $table->string('api_id', 255);
            $table->string('api_provider', 32);
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->text('description');
            $table->tinyInteger('is_active')->default("1");
            $table->timestamps();

            $table->foreign('id_preview')->references('id')->on('vis_images')->onDelete('set null')->onUpdate('set null');

        });

        Schema::create('vis_video_galleries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->timestamp('event_date');
            $table->tinyInteger('is_active');
            $table->timestamps();
        });

        Schema::create('vis_videos2video_galleries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('id_video')->unsigned();
            $table->integer('id_video_gallery')->unsigned();
            $table->tinyInteger('is_preview');
            $table->tinyInteger('priority');


            $table->foreign('id_video')->references('id')->on('vis_videos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_video_gallery')->references('id')->on('vis_video_galleries')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('vis_documents', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('file_folder');
            $table->text('file_source');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->tinyInteger('is_active')->default("1");
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
        Schema::dropIfExists('vis_tags2entities');
        Schema::dropIfExists('vis_images2galleries');
        Schema::dropIfExists('vis_images');
        Schema::dropIfExists('vis_galleries');
        Schema::dropIfExists('vis_tags');
        Schema::dropIfExists('vis_videos2video_galleries');
        Schema::dropIfExists('vis_video_galleries');
        Schema::dropIfExists('vis_videos');
        Schema::dropIfExists('vis_documents');


    }
}
