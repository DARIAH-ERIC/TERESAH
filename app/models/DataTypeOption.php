<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Watson\Validating\ValidatingTrait;

/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 10:41
 */
class DataTypeOption extends BaseModel
{
    use SoftDeletingTrait;
    use ValidatingTrait;

    protected $dates = array("deleted_at");
    protected $fillable = array(
        "data_types_id",
        "label",
        "value",
        "order"
    );

    /**
     * Validation rules for the model
     */
    protected $rules = array(
        "data_type_id" => "required|integer|exists:data_types,id,deleted_at,NULL",
        "label" => "required|max:255",
        "slug" => "required|max:255",
        "value" => "required|max:255",
        "order" => "required|integer"
    );

    public static function boot()
    {
        self::observe(new DataTypeOptionObserver);

        parent::boot();
    }

    public function dataType()
    {
        return $this->belongsTo("DataType");
    }
}