<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchemaLinkableToDataTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table("data_types", function(Blueprint $table)
        {
            $table->boolean("schema_linkable")->after("linkable")->default(true);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table("data_types", function(Blueprint $table) {
            $table->dropColumn("schema_linkable");
        });
	}

}
