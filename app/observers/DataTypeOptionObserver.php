<?php

/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 10:45
 */
class DataTypeOptionObserver
{
    public function saving($dataTypeOption)
    {
        $dataTypeOption->slug = BaseHelper::generateSlug($dataTypeOption->label);
    }
}