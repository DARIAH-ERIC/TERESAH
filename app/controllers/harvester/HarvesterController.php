<?php namespace Harvester;

use BaseController;
use Data;
use DataSource;
use DataType;
use DateTime;
use Goutte\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
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
        $crawler->filter('article[vocab="http://schema.org/"][typeof="SoftwareApplication"]')->each(function (Crawler $node) use($dataTypes, $sourceId) {
            Log::info("Passed it");
            $selectedTool = $node->filter("*[property='http://purl.org/dc/terms/title']")->each(function (Crawler $subNode) {
                $toolFound = $this->toolService->findByName($subNode->text());
                if(! $toolFound) {
                    $this->toolService->create($this->inputWithAuthenticatedUserId(array("name" => $subNode->text())));
                    $toolFound = $this->toolService->findByName($subNode->text());
                }
                return $toolFound;
            });
            if($selectedTool) {
                Log::info("We have a tool on this page, it contains at least a name");
                $myTool = $selectedTool[0];
                $this->toolService->attachDataSource($myTool->id, $sourceId);
                foreach ($dataTypes as $dataType) {
                    $rdfFullUri = $dataType->rdf_mapping;
                    if(! Str::startsWith($rdfFullUri, "http://")) {
                        $rdfFullUri = "http://schema.org/" . $rdfFullUri;
                    }
                    $node->filter('*[property="' . $rdfFullUri . '"]')->each(function (Crawler $subNode) use ($dataType, $myTool, $sourceId, $rdfFullUri) {
                        Log::info($rdfFullUri . ' --> ' . $subNode->text());

                        $dataFound = $this->dataService->findByValueAndTool($myTool->id, $subNode->text());
                        if(! $dataFound) {
                            $correctWithDataTypeOption = false;
                            if($dataType->dataTypeOption()->count() > 0) {
                                foreach ($dataType->dataTypeOption()->get() as $dataTypeOption) {
                                    if ($subNode->text() == $dataTypeOption->value) {
                                        $correctWithDataTypeOption = true;
                                    }
                                }
                            } else {
                                $correctWithDataTypeOption = true;
                            }
                            if($correctWithDataTypeOption) {
                                $d = new Data;
                                $d->fill([
                                    "value" => $subNode->text(),
                                    "tool_id" => $myTool->id,
                                    "data_type_id" => $dataType->id,
                                    "data_source_id" => $sourceId,
                                    "user_id" => Auth::user()->id,
                                    "created_at" => new DateTime,
                                    "updated_at" => new DateTime
                                ]);
                                $d->save();
                                Log::info("Saved data id:" . $d->id);
//                                $dataFound = $this->dataService->find($d->id);
                            }
                        }
                    });
                }
                if($myTool->isFilledSingle()) {
                    $myTool->is_filled = true;
                    $myTool->save();
                }
            }
        });

        return View::make("harvester.show")->with("harvest", "The harvest was complete");
    }
}