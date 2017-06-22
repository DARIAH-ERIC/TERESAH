<?php namespace Services;

use Repositories\ToolRepositoryInterface as ToolRepository;
use Services\ToolServiceInterface;
use Tool;

class ToolService extends AbstractRepositoryService implements ToolServiceInterface
{
    protected $errors;
    protected $toolRepository;

    public function __construct(ToolRepository $toolRepository)
    {
        $this->toolRepository = $this->setRepository($toolRepository);
    }

    public function create($input)
    {        
        return $this->repository->create($input);        
    }
    
    public function attachDataSource($id, $dataSourceId)
    {
        # TODO: Check for the existing relationship
        return $this->toolRepository->attachDataSource($id, $dataSourceId);
    }

    public function detachDataSource($id, $dataSourceId)
    {
        return $this->toolRepository->detachDataSource($id, $dataSourceId);
    }

    public function byAlphabet($startsWith)
    {
        return $this->toolRepository->byAlphabet($startsWith);
    }

    public function byFacet($type, $value)
    {
        return $this->toolRepository->byFacet($type, $value);
    }

    public function search($parameters)
    {
        return $this->toolRepository->search($parameters);
    }

    public function quicksearch($query)
    {
        return $this->toolRepository->quicksearch($query);
    }

    public function findWithAssociatedData($id)
    {        
        if (!is_numeric($id)) {
            $id = $this->toolRepository->getFirstBy("slug", "=", $id)->id;
        }

        $with = array("user", "dataSources.data" => function($query) use($id) {
            $query->where("data.tool_id", "=", $id);
        }, "dataSources.data.user", "dataSources.data.dataType");

        return $this->toolRepository->find($id, $with);
    }

    public function findByName($name)
    {
        return $this->toolRepository->getFirstBy("name", "=", $name);
    }

    public function random()
    {
        return $this->toolRepository->random();
    }
    
    public function popular()
    {
        return $this->toolRepository->popular();
    }
    
    public function latest()
    {
        return $this->toolRepository->latest();
    }
    
    public function allIncludingSourceLess(array $with = array(), $perPage = null)
    {
        return $this->repository->allIncludingSourceLess($with, $perPage);
    }
    
    public function mostViwed()
    {
        return $this->toolRepository->mostViwed();
    }

    public function refreshIsFilledVariable($toolId)
    {
        $tool = Tool::find($toolId);
        if($tool->is_filled == false && $tool->isFilledSingle()) {
            $tool->is_filled = true;
            $tool->save();
        } else if($tool->is_filled == true && ! $tool->isFilledSingle()) {
            $tool->is_filled = false;
            $tool->save();
        }
    }
}
