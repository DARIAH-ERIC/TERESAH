<?php

class DataTableSeeder extends Seeder
{ 
    public function run()
    {
        $dataSourceId = DataSource::first()->id;
        $userId = User::first()->id;
                
        $data = DatabaseSeeder::csv_to_array(app_path().'/database/seeds/data/data.csv', ';');
        
        DB::table("data")->delete();

        $dataTypes = DataType::all();
        $types = array();
        foreach($dataTypes as $dataType) {
            $types[$dataType->slug] = $dataType;
        }
        
        $dataSources = DataSource::all();
        $sources = array();
        foreach($dataSources as $dataSource) {
            $sources[$dataSource->name] = $dataSource->id;
        }
        
        $toolsTemp = Tool::all();
        
        $tools = array();
        foreach($toolsTemp as $tool) {
            $tools[$tool->name] = $tool->id;
            Tool::find($tool->id)->dataSources()->attach($dataSourceId);

            //By default, from TERESAH default data.csv, all entries are tools
            Tool::find($tool->id)->dataSources()->attach($sources["HaS Tool Registry"]);
            $toolTypeData["tool_id"] = $tool->id;
            $toolTypeData["data_type_id"] = $types["tool-type"]->id;
            $toolTypeData["data_source_id"] = $sources["HaS Tool Registry"];
            $toolTypeData["value"] = "Tool";
            $toolTypeData["user_id"] = $userId;
            $toolTypeData["created_at"] = new DateTime;
            $toolTypeData["updated_at"] = new DateTime;
            Data::create($toolTypeData);
        }
                
        foreach ($data as $d) {
            if(array_key_exists($d["tool"], $tools)){
                $correctWithDataTypeOption = false;
                $d["tool_id"] = $tools[$d["tool"]];
                unset($d["tool"]);
                $d["data_type_id"] = $types[$d["type"]]->id;
                if($types[$d["type"]]->dataTypeOption()->count() > 0) {
                    foreach($types[$d["type"]]->dataTypeOption()->get() as $dataTypeOption) {
                        if($dataTypeOption->value == $d["value"]) {
                            $correctWithDataTypeOption = true;
                        }
                    }
                } else {
                    $correctWithDataTypeOption = true;
                }
                unset($d["type"]);

                $d["data_source_id"] = $sources[$d["source"]];
                $t = Tool::find($d["tool_id"]);
                if(! in_array($d["data_source_id"], $t->dataSources()->lists('data_source_id'))){
                    Tool::find($d["tool_id"])->dataSources()->attach($d["data_source_id"]);
                }
                
                unset($d["source"]);

                $d["user_id"] = $userId; 
                $d["created_at"] = new DateTime;
                $d["updated_at"] = new DateTime;

                if($correctWithDataTypeOption) {
                    Data::create($d);
                }
            }
        }

        $mandatoryDataTypes = DataType::whereIn("slug", Tool::$mandatoryFieldSlugs)->get();
        foreach($toolsTemp as $tool) {
            $myTool = Tool::find($tool->id);
            if($myTool->isFilledBatch($mandatoryDataTypes)) {
                $myTool->is_filled = true;
                $myTool->save();
            }
        }
    }
    

}
