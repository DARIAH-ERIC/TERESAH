<?php namespace Repositories;

interface DataRepositoryInterface extends RepositoryInterface
{
    public function findByValueAndTool($toolId, $name);
}
