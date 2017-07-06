<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Support\Facades\Log;
use Watson\Validating\ValidatingTrait;

class Tool extends BaseModel
{
    use SoftDeletingTrait;
    use ValidatingTrait;

    protected $dates = array("deleted_at");
    protected $fillable = array("name", "user_id", "is_filled");
    public static $mandatoryFieldSlugs = array("tool-type", "description", "url", "operating-system");

    public static $missingMandatoryFields = array();

    /**
     * Validation rules for the model
     */
    protected $rules = array(
        "name" => "required|unique:tools|max:255",
        "slug" => "required|unique:tools|max:255",
        "is_filled" => "boolean",
        "user_id" => "required|integer|exists:users,id,deleted_at,NULL"
    );

    public static function boot()
    {
        self::observe(new ActivityObserver);
        self::observe(new ToolObserver);

        parent::boot();
    }

    public function activity()
    {
        return $this->morphMany("Activity", "target");
    }

    public function activities()
    {
        return $this->hasMany("Activity");
    }

    public function data()
    {
        return $this->hasMany("Data");
    }

    public function dataSources()
    {
        return $this->belongsToMany("DataSource", "tool_data_sources")->withTimestamps();
    }

    public function dataTypes()
    {
        return $this->hasManyThrough("DataType", "Data");
    }

    public function user()
    {
        return $this->belongsTo("User");
    }

    public function users()
    {
        return $this->belongsToMany("User")->withTimestamps();
    }

    public function similarTools()
    {
        return $this->belongsToMany("Tool", "similar_tools", "tool_id")->withTimestamps();
    }

    public function allSimilarTools()
    {
        $linked = $this->similarTools()->get();
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            $computed = $this->computedMatch()->get();
        } else {
            $computed = $this->computedMatch()->where("is_filled", true)->get();
        }
        $counter = 0;

        while(count($linked) < Config::get("teresah.similar_count") && $counter < count($computed))
        {
            $linked[] = $computed[$counter];
            $counter++;
        }

        return $linked;
    }
    
    public function incrementViews()
    {
        $this->views += 1;
        $this->save();
    }

    public function scopeHaveData($query)
    {
        return $query->has("data", ">", 0);
    }
    
    public function scopeHasUsers($query)
    {
        return $query->has("users", ">", 0);
    }    
    
    public function scopeHaveDataValueLike($query, $value)
    {
        return $query->whereHas("data", function($query) use($value){
            $query->where("value", "like", "%$value%");
        });
    }

    public function scopeMatchingString($query, $value)
    {
        return $query->where("name", "LIKE", "%$value%")->orWhereHas("data", function($query) use($value){
            $query->where("value", "LIKE", "%$value%");
        });
    }

    public function scopeComputedMatch($query)
    {
        $computed = Tool::haveData()->select("tools.id", "tools.name", "tools.slug", DB::raw("COUNT(*) AS matches"))
                    ->join("data", "data.tool_id", "=", "tools.id")
                    ->whereRaw("CONCAT(data.data_type_id, data.slug) IN(SELECT CONCAT(d.data_type_id, d.slug) FROM data d WHERE d.tool_id = $this->id)")
                    ->groupBy("tools.id")
                    ->orderBy("matches", "DESC")
                    ->where("tools.id", "!=", $this->id)
                    ->get();

        $similar = array();
        foreach($computed as $c) {
            if($c["matches"] > 1) {
                $similar[] = $c["id"];
            }
        }

        if(count($similar) > 0){
            $query->whereIn("id", $similar);
        }
    }


    public function scopeHaveFacet($query, $data_type_id, $value)
    {
        return $query->whereHas("data", function($query) use($value, $data_type_id){
            $query->where("slug", $value)->where("data_type_id", $data_type_id);
        });
    }

    public function isFilledBatch($mandatoryDataTypes)
    {
        self::$missingMandatoryFields = array();
        foreach($mandatoryDataTypes as $mandatoryDataType) {
            if(! $this->data()->where("data_type_id", $mandatoryDataType->id)->exists()) {
                array_push(self::$missingMandatoryFields, $mandatoryDataType->slug);
            }
        }
        if(!empty(self::$missingMandatoryFields)) {
            LOG::debug("Size of missing mandatory fields: ".sizeof(self::$missingMandatoryFields));
        }
        foreach(self::$missingMandatoryFields as $missingMandatoryField) {
            LOG::debug("This tool is missing: ".$missingMandatoryField);
        }

        return empty(self::$missingMandatoryFields);
    }

    public function isFilledSingle()
    {
        $mandatoryDataTypes = DataType::whereIn("slug", self::$mandatoryFieldSlugs)->get();
        return $this->isFilledBatch($mandatoryDataTypes);
    }

    public function getNumberOfUsers()
    {
        return count($this->users());
    }
    
    public function getAbbreviationAttribute()
    {
        return substr(preg_replace("~\b(\w)|.~", "$1", $this->name), 0, 4);
    }

    /*
     * Returns first found description for this tool
     */
    public function getDescription()
    {
        foreach ($this->dataSources as $data_source)
        {
            $description = $data_source->getLatestToolDataFor($this->id, "description");
            if(!empty($description))
            {
                return $description;
            }
        }
        return null;
    }
}
