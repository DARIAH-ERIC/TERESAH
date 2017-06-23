<?php
/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 12:33
 */

namespace Services;


class DataTypeOptionService extends AbstractRepositoryService implements DataTypeOptionServiceInterface
{
    protected $errors;
    protected $dataTypeOptionRepository;

    public function __construct(DataTypeOptionRepository $dataTypeOptionRepository)
    {
        $this->dataTypeOptionRepository = $this->setRepository($dataTypeOptionRepository);
    }

    public function getDataTypeOptions()
    {
        return $this->dataTypeOptionRepository->lists("label", "id");
    }
}