<?php namespace Harvester;

use BaseController;
use Goutte\Client;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Services\DataTypeService;
use Services\ToolService;
use Symfony\Component\DomCrawler\Crawler;

class HarvesterController extends BaseController
{
    protected $toolService;
    protected $dataTypeService;

    public function __construct(ToolService $toolService, DataTypeService $dataTypeService)
    {
        parent::__construct();
        $this->toolService = $toolService;
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

        $client = new Client();
        $crawler = $client->request('GET', urldecode($url));
        $crawler->filter('article[itemtype="http://schema.org/SoftwareApplication"]')->each(function (Crawler $node) {
            $dataTypes = $this->dataTypeService->all()->get();

            $node->filter('*[property="http://purl.org/dc/terms/title"]')->each(function (Crawler $subNode) {
                Log::info('http://purl.org/dc/terms/title --> ' . $subNode->text());
            });
            foreach($dataTypes as $dataType) {
                $node->filter('*[property="' . $dataType->rdf_mapping . '"]')->each(function (Crawler $subNode) use($dataType) {
                    Log::info($dataType->rdf_mapping . ' --> ' . $subNode->text());
                });
            }
        });


//        $parameters[] = array();
//        $parameters["query"] = "postgresql";
//        $results = $this->toolService->search($parameters);
//        $tools = $results["tools"];
//        foreach ($tools as $tool) {
//            Log::info($tool->name);
//        }

        return View::make("harvester.show");
    }
}