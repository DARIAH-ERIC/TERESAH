<?php
/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 12:37
 */

namespace Repositories\Eloquent;


use DataTypeOption;
use Repositories\DataTypeOptionRepositoryInterface;

class DataTypeOptionRepository extends AbstractRepository implements DataTypeOptionRepositoryInterface
{
    protected $model;

    public function __construct(DataTypeOption $dataTypeOption)
    {
        $this->model = $dataTypeOption;
    }

    public function lists($column = "label", $key = "id")
    {
        return $this->model->orderBy($column, "ASC")->lists($column, $key);
    }
}