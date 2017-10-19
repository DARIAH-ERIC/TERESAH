<?php namespace Api\V1;

use Api\ApiController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Services\HarvesterServiceInterface as HarvesterService;
use Teresah\Support\Facades\Response;

class HarvestersController extends ApiController
{
    protected $accessControlList = array(
        "administrator" => array("*")
    );

    protected $harvesterService;

    public function __construct(HarvesterService $harvesterService)
    {
        parent::__construct();

        $this->harvesterService = $harvesterService;
    }

    /**
     * Display a listing of available Harvesters.
     *
     * GET /api/v1/harvesters.json(?limit=20)
     *
     * @api
     * @example documentation/api/v1/harvesters.md
     * @return Response
     */
    public function index()
    {
        return Response::jsonWithStatus(200, array("harvesters" => $this->harvesterService->all($with = array("user", "dataSources"), $perPage = Input::get("limit", 20))->toArray()));
    }

    /**
     * Return the specified Harvester.
     *
     * GET /api/v1/tools/{id}.json
     *
     * @api
     * @example documentation/api/v1/harvesters.md
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        return Response::jsonWithStatus(200, $this->harvesterService->findWithAssociatedData($id)->toArray());
    }
}
