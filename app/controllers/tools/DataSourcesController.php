<?php namespace Tools;

use DataSource;
use Tool;
use BaseController;
use Illuminate\Support\Facades\View;

class DataSourcesController extends BaseController
{
    protected $skipAuthentication = array("show");
    protected $tool;
    protected $dataSource;
    protected $dataSources;

    public function __construct(Tool $tool, DataSource $dataSource)
    {
        parent::__construct();

        $this->tool = $tool;
        $this->dataSource = $dataSource;
        $this->dataSources = $dataSource;
    }

    /**
     * Show the specified Data Source under the specified Tool.
     *
     * GET /tools/{toolId}/data-sources/{id}
     *
     * @param  int $toolId
     * @param  int $id
     * @return View
     */
    public function show($toolId, $id)
    {
        $this->tool = $this->tool->find($toolId);
        $this->dataSources = $this->tool->dataSources()
            ->orderBy("data_sources.name", "ASC")->get();
        $this->dataSource = $this->tool->dataSources->find($id);

        return View::make("tools.data_sources.show")
            ->with("tool", $this->tool)
            ->with("dataSources", $this->dataSources)
            ->with("dataSource", $this->dataSource)
            ->with("dataSourceData", $this->tool->data()->where("data_source_id", $this->dataSource->id)->get());
    }
}
