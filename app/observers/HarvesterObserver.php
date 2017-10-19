<?php

class HarvesterObserver
{
    public function saving($harvester)
    {
        $harvester->slug = BaseHelper::generateSlug($harvester->label);
    }
}
