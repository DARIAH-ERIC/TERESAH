<?php namespace Services;

interface HarvesterServiceInterface extends RepositoryServiceInterface
{
    public function create($input);

    public function attachDataSource($id, $dataSourceId);

    public function findAllUrgent();

    public function findWithAssociatedData($id);
}
