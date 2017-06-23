<?php
/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 12:37
 */

namespace Repositories;


interface DataTypeOptionRepositoryInterface extends RepositoryInterface
{
    public function lists($column, $key);
}