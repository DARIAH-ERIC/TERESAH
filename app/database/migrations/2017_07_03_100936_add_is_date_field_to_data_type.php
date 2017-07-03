<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDateFieldToDataType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table("data_types", function(Blueprint $table)
        {
            $table->boolean("is_date_field")->after("schema_linkable")->default(false);
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
            $table->dropColumn("is_date_field");
        });
	}

}
