<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 10:52
 */
class AddDataTypeOptionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("data_type_options", function($table)
        {
            $table->increments("id");
            $table->string("label", 255)->unique();
            $table->string("slug", 255)->unique();
            $table->string("value", 255)->unique();
            $table->integer("order")->unsigned();
            $table->integer("data_type_id")->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("data_type_options", function($table)
        {
            DB::statement(
                "ALTER TABLE data_type_options 
                    ADD CONSTRAINT fk_data_type_options_data_type_id
                    FOREIGN KEY (data_type_id) 
                    REFERENCES data_types(id) 
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
        Schema::drop("data_type_options");
    }

}