<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Watson\Validating\ValidatingTrait;

class Harvester extends BaseModel
{
    use ValidatingTrait;

    protected $dates = array("last_launched");
    protected $fillable = array(
        "data_source_id",
        "label",
        "url",
        "last_launched",
        "active",
        "launch_now",
        "user_id"
    );

    /**
     * Validation rules for the model
     */
    protected $rules = array(
        "data_source_id" => "required|integer|exists:data_sources,id",
        "label" => "required|unique:data_types|max:255",
        "slug" => "required|unique:data_types|max:255",
        "url" => "required|url|max:1024",
        "last_launched" => "sometimes|date",
        "active" => "required|boolean",
        "launch_now" => "required|boolean",
        "user_id" => "required|integer|exists:users,id,deleted_at,NULL"
    );

    public static function boot()
    {
        self::observe(new ActivityObserver);
        self::observe(new HarvesterObserver);

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

    public function dataSource()
    {
        return $this->belongsTo("DataSource");
    }

    public function scopeIsActive($query)
    {
        return $query->where("active", true);
    }

    public function user()
    {
        return $this->belongsTo("User");
    }
}
