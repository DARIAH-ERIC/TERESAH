<?php

class DataController extends BaseController {

    protected $skipAuthentication = array("valuesByType", "dataCloud", "quicksearch");
    protected $data;

    public function __construct(Data $data) {
        parent::__construct();

        $this->data= $data;
    }
    
    /**
     * Show data values matching a specific dataType
     * 
     * @param type $type slug for the dataType
     * @return View
     */
    public function valuesByType($type){
        $dataType = DataType::where("slug", $type)->first();
        
        $dataValues = $this->data->where("data_type_id", $dataType->id)
                            ->groupBy("slug")
                            ->orderBy("value", "ASC")->paginate(20);
        $allDataTypesOptions = $dataType->dataTypeOption()->get();
        $dataTypesOptionsMap[] = array();
        foreach($allDataTypesOptions as $dataTypesOption) {
            $dataTypesOptionsMap[$dataTypesOption->value] = $dataTypesOption->label;
        }
        return View::make("tools.by-facet.by-type", compact("dataValues"))
            ->with("dataType", $dataType)->with("dataTypesOptionsMap", $dataTypesOptionsMap);
    }
    
    /**
     * Search data value for quicksearch
     * 
     * @param type $query string to match in tool name
     * @return json
     */
    public function quicksearch($query) {
        $matches = $this->data
                    ->with("dataType")

                    ->whereHas('dataType', function($q){
                        $q->where('linkable', '1');
                    })
                    ->where("value", "LIKE" ,"%$query%")
                    ->groupBy("slug", "data_type_id")
                    ->orderBy("value", "ASC")
                    ->take(5)->get();
                    
        $result = array();
        foreach($matches as $match) {
            $obj = new stdClass();
            $obj->name = $match->value;
            $obj->type = $match->data_type->label;
            $obj->url = route('tools.by-facet', array($match->data_type->slug, $match->slug));
            $result[] = $obj;
        }
        return $result;
    }
    
    /**
     * Get the data used for the jqCloud
     * @return json
     */
    public function dataCloud(){
        $result = array();
        
        $dataTypes = DataType::isLinkable()->get();
        
        $base = URL::to('tools/by-facet');
        
        foreach ($dataTypes as $type) {
            $dataResult = $this->data->select("data.value AS text", 
                                         DB::raw("CONCAT('".$base."/', '".$type->slug."', '/', slug) AS link"),
                                         DB::raw("COUNT(slug) AS weight"))
                                    ->where("data_type_id", $type->id)                                    
                                    ->groupBy("slug")->get()->toArray();
            $result = array_merge($dataResult, $result);
        }
        
        $return = array();
        foreach($result as $value)
        {
            if($value["weight"] >= Config::get("teresah.word_cloud_threshold"))
                $return[] = $value;           
        }
                
        usort($return, function($a, $b){
            return $b["weight"] - $a["weight"];
        });
        
        array_splice($return, Config::get("teresah.word_cloud_count"));
        return $return;
    }
}