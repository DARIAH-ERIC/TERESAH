<?php namespace Harvester;

use BaseController;
use Data;
use DataSource;
use DateTime;
use Goutte\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Services\DataService;
use Services\DataTypeService;
use Services\ToolService;
use Symfony\Component\DomCrawler\Crawler;
use Tool;

class HarvesterController extends BaseController
{
    protected $toolService;
    protected $dataService;
    protected $dataTypeService;

    public function __construct(ToolService $toolService, DataService $dataService, DataTypeService $dataTypeService)
    {
        parent::__construct();
        $this->toolService = $toolService;
        $this->dataService = $dataService;
        $this->dataTypeService = $dataTypeService;
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
        return View::make("harvester.show");
    }

    /**
     * Show the index page of the harvester.
     *
     * POST /harvester
     *
     * @param url
     * @return View
     */
    public function harvest()
    {
        $url = Input::get("url");
        Log::info("The URL to crawl: " . $url);
        $dataTypes = $this->dataTypeService->all()->get();

        $dataSources = DataSource::all();
        foreach($dataSources as $dataSource) {
            if($dataSource->slug == "has-tool-registry") {
                $sourceId = $dataSource->id;
            }
        }

        $client = new Client();
        $crawler = $client->request('GET', urldecode($url));
        $crawler->filter('article[itemtype="http://schema.org/SoftwareApplication"]')->each(function (Crawler $node) use($dataTypes, $sourceId) {
            $selectedTool = $node->filter('*[itemprop="name"]')->each(function (Crawler $subNode) {
                Log::info('Create tool with name: --> ' . $subNode->text());

                $toolFound = $this->toolService->findByName($subNode->text());
                if(! $toolFound) {
                    $this->toolService->create($this->inputWithAuthenticatedUserId(array("name" => $subNode->text())));
                    $toolFound = $this->toolService->findByName($subNode->text());
                }
                Log::info("Tool found: " . $toolFound->id);
                return $toolFound;
            });
            if($selectedTool) {
                Log::info("We have a tool on this page, it contains at least a name");
                $myGoodTool = $selectedTool[0];
                $this->toolService->attachDataSource($myGoodTool->id, $sourceId);
                foreach ($dataTypes as $dataType) {
                    $node->filter('*[property="' . $dataType->rdf_mapping . '"]')->each(function (Crawler $subNode) use ($dataType, $myGoodTool, $sourceId) {
                        Log::info($dataType->rdf_mapping . ' --> ' . $subNode->text());

                        $dataFound = $this->dataService->findByValue($subNode->text());
                        if(! $dataFound) {
                            $this->dataService->create($this->inputWithAuthenticatedUserId(array("name" => $subNode->text())));
                            $d = new Data;
                            $d->fill([
                                "value" => $subNode->text(),
                                "tool_id" => $myGoodTool->id,
                                "data_type_id" => $dataType->id,
                                "data_source_id" => $sourceId,
                                "user_id" => Auth::user()->id,
                                "created_at" => new DateTime,
                                "updated_at" => new DateTime
                            ]);
                            $d->save();
                            Log::info($d->id);

                            $dataFound = $this->dataService->findByValue($subNode->text());
                        }
                        Log::info("We have this data in the DB: " . $dataFound->id);
                    });
                }
            }
        });

        return View::make("harvester.show");
    }
}