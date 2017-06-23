<?php

/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 10:58
 */
class DataTypeOptionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table("data_type_options")->delete();

        $dataTypes = DataType::all();
        $types = array();
        foreach($dataTypes as $dataType) {
            $types[$dataType->slug] = $dataType->id;
        }

        $dataTypeOptions = array(
            array("label" => "Encoding",
                "value" => "Encoding",
                "order" => 1,
                "data_type_id" => 1
            ),
            array("label" => "Gamification > Dissemination-Crowdsourcing",
                "value" => "Gamification > Dissemination-Crowdsourcing",
                "order" => 2,
                "data_type_id" => 1
            ),
            array("label" => "Georeferencing > Enrichment-Annotation",
                "value" => "Georeferencing > Enrichment-Annotation",
                "order" => 3,
                "data_type_id" => 1
            )
        );
        foreach ($dataTypeOptions as $dataTypeOption) {
            $dataTypeOption["data_type_id"] = $types["application-category"];
            DataTypeOption::create($dataTypeOption);
        }
    }
}