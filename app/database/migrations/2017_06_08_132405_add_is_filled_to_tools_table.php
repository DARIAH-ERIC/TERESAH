<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFilledToToolsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table("tools", function(Blueprint $table)
        {
            $table->boolean("is_filled")->after("views")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table("tools", function(Blueprint $table) {
            $table->dropColumn("is_filled");
        });
    }

}
