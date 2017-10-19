<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHarvesterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create("harvesters", function($table) {
            $table->increments("id");
            $table->integer("data_source_id")->unsigned();
            $table->string("label", 255)->unique();
            $table->string("slug", 255)->unique();
            $table->text("url");
            $table->boolean("active")->default(true);
            $table->boolean("launch_now")->default(true);
            $table->timestamp("last_launched")->nullable();
            $table->integer("user_id")->unsigned();
            $table->timestamps();
        });

        Schema::table("harvesters", function($table) {
            DB::statement(
                "ALTER TABLE harvesters
                    ADD CONSTRAINT fk_harvesters_data_sources
                    FOREIGN KEY (data_source_id) 
                    REFERENCES data_sources(id) 
                    ON DELETE CASCADE"
            );
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop("harvesters");
	}
}
