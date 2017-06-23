<?php
/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 12:33
 */

namespace Services;


interface DataTypeOptionServiceInterface extends RepositoryServiceInterface
{
    public function getDataTypeOptions();
}