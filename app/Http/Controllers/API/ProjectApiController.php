<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\AppBaseController;
use App\User;
use App\Task;
use App\Project;
use App\TaskFiles;
use Illuminate\Http\Request;
//use App\Http\Requests\API\ApiTaskRequest;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Lang;
use Response;
class ProjectApiController extends AppBaseController
{
    public function index(Request $request)
    {
        
		$project = Project::orderBy('created_at', 'desc')->get();
		
      
        
        return $this->sendResponse($project->toArray());
    }

    public function update(Request $request,$id)
    {
        $project = Project::find($id);
        
        $user = auth()->user();
		
        if (!$user) {
            return $this->sendError('Please login !', 401);
        }
        $inputs = $request->all();
        $project->update($inputs);
        return $this->sendResponse($project->toArray(), 'Project updated successfully');
    }

    public function getMyProjects(){

        $user = auth()->user();
        $projects = Project::orderBy('created_at', 'desc')->where("user_id",$user->id)->get();
        return $this->sendResponse($projects->toArray());

    }

    public function create(Request $request){
    	

     

       
        $user = auth()->user();
        if (!$user) {
            return $this->sendError('Please login !', 401);
        }

       $project = Project::create([
                'project_name' => $request->project_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
             
            ]);

       
        

        
        return $this->sendResponse($project->toArray(), 'Project created successfully');
    }

    public function search_projects(Request $request)
    {
        $project_name = $request['project_name'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $projects    = Project::where(function($q) use($project_name,$start_date,$end_date) {
          $q->orWhere('project_name', 'LIKE' , '%'.$project_name.'%')->orWhere('start_date', 'LIKE' , '%'.$start_date.'%')->orWhere('end_date', 'LIKE' , '%'.$end_date.'%');
        })->paginate($this->default_paginate_number);
        
        return $this->sendResponse(compact('projects','keyword'));
    }
}    