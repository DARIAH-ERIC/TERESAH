<?php namespace Services;

use Repositories\HarvesterRepositoryInterface as HarvesterRepository;
use Services\HarvesterServiceInterface;
use Harvester;

class HarvesterService extends AbstractRepositoryService implements HarvesterServiceInterface
{
    protected $errors;
    protected $harvesterRepository;

    public function __construct(HarvesterRepository $harvesterRepository)
    {
        $this->harvesterRepository = $this->setRepository($harvesterRepository);
    }

    public function create($input)
    {
        return $this->repository->create($input);
    }

    public function attachDataSource($id, $dataSourceId)
    {
        return $this->harvesterRepository->attachDataSource($id, $dataSourceId);
    }

    public function findAllUrgent()
    {
        return $this->harvesterRepository->findAllUrgent();
    }

    public function findWithAssociatedData($id)
    {
        return $this->harvesterRepository->find($id, array("user", "dataSource", "dataSource.user"));
    }
}