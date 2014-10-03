<?php

class ToolUsageController extends \BaseController {

	/**
	 * Display a listing of the most popular tools
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

        
	/**
	 * Creates a usage of a tool by a user
	 *
	 * @return Response
	 */
	public function create($toolID)
	{
            Auth::user()->toolUsages()->attach($toolID);
            
            return Response::json(array(
                "status" => 200, 
                "action" => "DELETE",
                "callback" => URL::route("tools.unuse", array("toolID" => $toolID)),
                "title" => Lang::get("views/tools/data_sources/show.unuse.title")
                ), 200);
	}


	/**
	 * Remove the usage of a tool by a user
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($toolID)
	{            
            Auth::user()->toolUsages()->detach($toolID);
            
            return Response::json(array(
                "status" => 200, 
                "action" => "GET",
                "callback" => URL::route("tools.use", array("toolID" => $toolID)),
                "title" => Lang::get("views/tools/data_sources/show.use.title")
                ), 200);
	}


}
