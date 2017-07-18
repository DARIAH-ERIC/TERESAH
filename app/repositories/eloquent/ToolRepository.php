<?php namespace Repositories\Eloquent;

use ArgumentsHelper;
use Tool;
use Data;
use DataType;
use Illuminate\Support\Facades\Config;
use Repositories\ToolRepositoryInterface;
use Repositories\Eloquent\AbstractRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as DB;

class ToolRepository extends AbstractRepository implements ToolRepositoryInterface
{
    protected $model;

    public function __construct(Tool $tool)
    {
        $this->model = $tool;
    }

    public function create($input)
    {
        if($this->model->fill($input)->save())
        {            
            return array("success" => true, "id" => $this->model->id);
        }
        else
        {
            return array("success" => false, "errors" => $this->model->getErrors());
        }
    }
    
    public function all(array $with = array(), $perPage = null)
    {
        # TODO: Add support for ordering with multiple columns
        # TODO: Extract/remove the pagination?

        if (isset($perPage) && is_numeric($perPage)) {
            if (Auth::check() && Auth::user()->hasAdminAccess()) {
                return $this->make($with)->haveData()->orderBy("created_at", "DESC")->paginate($perPage);
            } else {
                return $this->make($with)->haveData()->where("is_filled", true)->orderBy("created_at", "DESC")->paginate($perPage);
            }
        }
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return $this->make($with)->haveData()->orderBy("created_at", "DESC");
        } else {
            return $this->make($with)->haveData()->where("is_filled", true)->orderBy("created_at", "DESC");
        }
    }
    
    public function allIncludingSourceLess(array $with = array(), $perPage = null)
    {
        if (isset($perPage) && is_numeric($perPage)) {
            if (Auth::check() && Auth::user()->hasAdminAccess()) {
                return $this->make($with)->orderBy("name", "ASC")->paginate($perPage);
            } else {
                return $this->make($with)->where("is_filled", true)->orderBy("name", "ASC")->paginate($perPage);
            }
        }
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return $this->make($with)->orderBy("name", "ASC");
        } else {
            return $this->make($with)->where("is_filled", true)->orderBy("name", "ASC");
        }
    }

    public function attachDataSource($id, $dataSourceId)
    {
        $this->model = $this->find($id);
        if(! $this->model->dataSources()->where("data_source_id", $dataSourceId)->exists()) {
            return $this->model->dataSources()->attach($dataSourceId);
        }
        return 0;
    }

    public function detachDataSource($id, $dataSourceId)
    {
        $this->model = $this->find($id);

        return $this->model->dataSources()->detach($dataSourceId);
    }

    public function byAlphabet($startsWith) {
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return $this->model->haveData()
                ->where("name", "LIKE", "$startsWith%")
                ->orderBy("name", "ASC")
                ->paginate(Config::get("teresah.browse_pager_size"));
        } else {
            return $this->model->haveData()
                ->where("name", "LIKE", "$startsWith%")
                ->where("is_filled", true)
                ->orderBy("name", "ASC")
                ->paginate(Config::get("teresah.browse_pager_size"));
        }
    }

    public function byFacet($type, $value)
    {
        $dataType = DataType::where("slug", $type)->first();
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return $this->model
                ->whereHas("data", function ($query) use ($dataType, $value) {
                    $query->where("slug", $value)
                        ->where("data_type_id", $dataType->id);
                })
                ->orderBy("name", "ASC")
                ->paginate(Config::get("teresah.browse_pager_size"));
        } else {
            return $this->model
                ->whereHas("data", function($query) use($dataType, $value) {
                    $query->where("slug", $value)
                        ->where("data_type_id", $dataType->id);
                })
                ->where("is_filled", true)
                ->orderBy("name", "ASC")
                ->paginate(Config::get("teresah.browse_pager_size"));
        }
    }

    public function quicksearch($query) {
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            $matches = $this->model
                ->select("name", "slug", "id")
                ->haveData()
                ->where("name", "LIKE", "%$query%")
                ->orderBy("name", "ASC")
                ->take(Config::get("teresah.quicksearch_size"))->get();
        } else {
            $matches = $this->model
                ->select("name", "slug", "id")
                ->haveData()
                ->where("is_filled", true)
                ->where("name", "LIKE" ,"%$query%")
                ->orderBy("name", "ASC")
                ->take(Config::get("teresah.quicksearch_size"))->get();
        }
        $result = array();

        foreach ($matches as $match) {
            $match->url = url("/")."/tools/".$match->slug;
            $result[] = $match;
        }

        return $result;
    }

    public function random() {
        $randomStr = "RAND()";
        if($_ENV["DATABASE_DRIVER"] == 'pgsql'){
            $randomStr = "RANDOM()";
        }
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return $this->model->haveData()->orderBy(DB::raw($randomStr))->first();
        } else {
            return $this->model->haveData()->where("is_filled", true)->orderBy(DB::raw($randomStr))->first();
        }
    }
    
    public function popular()
    {
        $result = DB::table("tool_user")
                    ->select("tool_id", DB::raw("COUNT(tool_id) AS weight"))
                    ->groupBy("tool_id")
                    ->orderBy("weight", "DESC")
                    ->take(3)
                    ->get();
        
        $return = array();
        foreach($result as $value)
        {
            $return[] = Tool::find($value->tool_id);
        }
        return $return;
    }
    
    public function latest() {
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return $this->model->haveData()->orderBy(DB::raw("created_at"), "DESC")->take(3)->get();
        } else {
            return $this->model->haveData()->where("is_filled", true)->orderBy(DB::raw("created_at"), "DESC")->take(3)->get();
        }
    }
    
    public function mostViwed(){
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            return $this->model->haveData()->orderBy("views", "DESC")->take(3)->get();
        } else {
            return $this->model->haveData()->where("is_filled", true)->orderBy("views", "DESC")->take(3)->get();
        }
    }

    public function search($parameters = array())
    {
        $tool_ids = array();

        $tool_id_query = $this->model->haveData();

        $types = DataType::IsLinkable()->haveData()->get();

        foreach ($types as $type) {
            if (array_key_exists($type->slug, $parameters)) {
                $values = ArgumentsHelper::getArgumentValues($type->slug);

                foreach ($values as $value){
                    $tool_id_query->haveFacet($type->id, $value);
                }
            }
        }

        if (!empty($parameters["query"])) {
            $query = $parameters["query"];
            $tool_ids = $tool_id_query->lists("id");

            if (count($tool_ids) > 0) {
                $string_match_query = $this->model->whereIn("id", $tool_ids);
            } else {
                $string_match_query = $this->model->haveData();
            }

            if (str_contains($query, " ")) {
                $parts = explode(" ", $query);
            } else{
                $parts = array($query);
            }

            foreach ($parts as $q) {
                $string_match_query->matchingString($q);
            }

            $string_matched_tool_ids = $string_match_query->lists("id");
            $tool_ids = array_intersect($string_matched_tool_ids, $tool_ids);
        } else {
            $tool_ids = $tool_id_query->lists("id");
        }

        if (empty($tool_ids)) {
            $tool_ids = array(0);
        }

        $facetList = array();

        foreach ($types as $type) {
            $result =  Data::select("value", "slug", DB::raw("count(tool_id) as total"))
                             ->where("data_type_id", $type->id);

            if (count($tool_ids) > 0) {
                $result->whereIn("tool_id", $tool_ids);
            }

            if (array_key_exists($type->slug."-limit", $parameters)) {
                $limit = $parameters[$type->slug."-limit"];
            } else {
                $limit = Config::get("teresah.search_facet_count");
            }

            $type->values = $result->groupBy("value")
                                   ->orderBy("total", "DESC")
                                   ->paginate($limit);
            $facetList[] = $type;
        }

        $limit = Config::get("teresah.search_pager_size");
        if(!empty($parameters["limit"])) {
            $limit = $parameters["limit"];
        }
        if (Auth::check() && Auth::user()->hasAdminAccess()) {
            $tools = $this->model->whereIn("id", $tool_ids)
                ->orderBy("name", "ASC")
                ->paginate($limit);
        } else {
            $tools = $this->model->whereIn("id", $tool_ids)
                ->where("is_filled", true)
                ->orderBy("name", "ASC")
                ->paginate($limit);
        }

        $results = array(
            "tools" => $tools,
            "facets" => $facetList
        );

        return $results;
    }
}
