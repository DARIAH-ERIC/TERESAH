<?php namespace Repositories\Eloquent;

use ArgumentsHelper;
use Harvester;
use Illuminate\Support\Facades\Config;
use Repositories\HarvesterRepositoryInterface;
use Repositories\Eloquent\AbstractRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as DB;

class HarvesterRepository extends AbstractRepository implements HarvesterRepositoryInterface
{
    protected $model;

    public function __construct(Harvester $harvester)
    {
        $this->model = $harvester;
    }

    public function create($input)
    {
        if ($this->model->fill($input)->save()) {
            return array("success" => true, "id" => $this->model->id);
        } else {
            return array("success" => false, "errors" => $this->model->getErrors());
        }
    }

    public function attachDataSource($id, $dataSourceId)
    {
        $this->model = $this->find($id);
        if (!$this->model->dataSources()->where("data_source_id", $dataSourceId)->exists()) {
            return $this->model->dataSources()->attach($dataSourceId);
        }
        return 0;
    }

    public function findAllUrgent()
    {
        return $this->model = $this->getManyBy("launch_now", "=", 1);
    }


}