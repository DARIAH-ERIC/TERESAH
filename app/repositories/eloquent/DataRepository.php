<?php namespace Repositories\Eloquent;

use Data;
use Repositories\DataRepositoryInterface;
use Repositories\Eloquent\AbstractRepository;

class DataRepository extends AbstractRepository implements DataRepositoryInterface
{
    protected $model;

    public function __construct(Data $data)
    {
        $this->model = $data;
    }

    public function findByValueAndTool($toolId, $name)
    {
        return $this->model->where("tool_id", "=", $toolId)->where("value", "=", $name)->first();
    }
}
