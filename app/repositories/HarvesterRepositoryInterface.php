<?php namespace Repositories;

interface HarvesterRepositoryInterface extends RepositoryInterface
{
    public function create($input);

    public function find($id);

    public function attachDataSource($id, $dataSourceId);

    public function findAllUrgent();
}