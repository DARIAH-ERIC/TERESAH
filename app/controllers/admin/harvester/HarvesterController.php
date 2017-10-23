<?php namespace Admin\Harvester;

use BaseController;
use BaseHelper;
use Data;
use DataSource;
use DateTime;
use Goutte\Client;
use Harvester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Services\DataService;
use Services\DataTypeService;
use Services\HarvesterService;
use Services\ToolService;
use Symfony\Component\DomCrawler\Crawler;
use Teresah\Support\Facades\Response;
use Tool;

class HarvesterController extends BaseController
{
    protected $toolService;
    protected $dataService;
    protected $dataTypeService;
    protected $harvesterService;

    protected $accessControlList = array(
        "supervisor" => array("*"),
        "administrator" => array("*")
    );

    private static $vocabSoftwareApplication = "http://schema.org/SoftwareApplication";

    public function __construct(ToolService $toolService, DataService $dataService, DataTypeService $dataTypeService, HarvesterService $harvesterService)
    {
        parent::__construct();
        $this->toolService = $toolService;
        $this->dataService = $dataService;
        $this->dataTypeService = $dataTypeService;
        $this->harvesterService = $harvesterService;
    }

    /**
     * Show the index page of the harvester.
     *
     * GET /harvester
     *
     * @return View
     */
    public function index()
    {
        $allDataSources = DataSource::all();
        $dataSources = array();
        foreach($allDataSources as $oneDataSource) {
            $dataSources[$oneDataSource->id] = $oneDataSource->name;
        }
        return View::make("admin.harvester.show")
            ->with("dataSources", $dataSources)
            ->with("harvesters", Harvester::all());
    }

    /**
     * Remove the specified Harvester information from storage.
     *
     * DELETE /admin/harvester/{id}
     *
     * @param url
     * @return View
     */
    public function destroy($id)
    {
        if ($this->harvesterService->destroy($id)) {
            return Redirect::route("admin.harvester.index")
                ->with("success", Lang::get("controllers.admin.harvester.destroy.success"));
        } else {
            return Redirect::route("admin.harvester.delete", $id)
                ->with("error", Lang::get("controllers.admin.harvester.destroy.error"));
        }
    }

    /**
     * Harvest the given URL and inputs the data in our database.
     * Create a new item if it doesn't yet exist
     *
     * POST /admin/harvester
     *
     * @param url
     * @return View
     */
    public function save()
    {
        $name = Input::get("name");
        $url = Input::get("url");
        $sourceId = Input::get("dataSource");

        $harvester = new Harvester();
        $harvester->fill($this->inputWithAuthenticatedUserId([
            "label" => $name,
            "url" => $url,
            "active" => true,
            "launch_now" => true,
            "data_source_id" => $sourceId
        ]));
        $harvester->save();

        return Redirect::route("admin.harvester.index")
            ->with("success", Lang::get("controllers.admin.harvester.save.success"));
    }

    /**
     * Harvest the given URL and inputs the data in our database.
     * Create a new item if it doesn't yet exist
     *
     * PUT/PATCH /admin/harvester/{id}
     *
     * @param url
     * @return View
     */
    public function harvest($id)
    {
        $formInput = array();
        $formInput["launch_now"] = 1;
        if ($this->harvesterService->update($id, $this->inputWithAuthenticatedUserId($formInput))) {
            return Redirect::route("admin.harvester.index")
                ->with("success", Lang::get("controllers.admin.harvester.harvest.success"));
        } else {
            return Redirect::route("admin.harvester.index")
                ->withErrors($this->harvestService->errors())->withInput();
        }
    }
}