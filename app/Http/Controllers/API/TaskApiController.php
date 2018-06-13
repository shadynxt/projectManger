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
class TaskApiController extends AppBaseController
{
    public function index(Request $request)
    {
        
		$task = Task::orderBy('created_at', 'desc')->get();
		
      
        
        return $this->sendResponse($task->toArray());
    }

    public function getMyTasks(){

        $user = auth()->user();
        $tasks = Task::orderBy('created_at', 'desc')->where("user_id",$user->id)->get();
        return $this->sendResponse($tasks->toArray());

    }

    public function create(Request $request){
    	//$services = Service::find($request->input('id'));

     

       
        $user = auth()->user();
        if (!$user) {
            return $this->sendError('Please login !', 401);
        }
       $task = new Task();
                $task->project_id = $request->project_id;
                $task->user_id   = $user->id;
                $task->task_title = $request->task_title;
                $task->task   = $request->task_desc;
                $task->priority   = $request->priority;
                $task->duedate  = $request->duedate;
            
      if( $request->photos ) {
                 foreach ($request->photos as $img){
                 $img = uploadImgFromMobile($img,'/uploads/projects/');
                 TaskFiles::create(['img'=>$img , 'task_id'=>$task->id]);
             }
            }

        /*if ($request['photos']) {
                $file = $request['photos'];
                if (is_file($file)) {
                    $fileModel = Files::uploadFile($file, $task);
                } else {
                    $ext = ($request->extension) ? $request->extension : 'jpg';
                    $fileModel = Files::uploadFile($file, $task, 'public', $ext);
                }
                $task->logo = $fileModel->url;
            }
        */

       $task->save();

       
        

        
        return $this->sendResponse($task->toArray(), 'Task created successfully');
    }
    public function update(Request $request,$id)
    {
        $task = Task::find($id);
        
        $user = auth()->user();
		
        if (!$user) {
            return $this->sendError('Please login !', 401);
        }
        $inputs = $request->all();
        $task->update($inputs);
        return $this->sendResponse($task->toArray(), 'Task updated successfully');
    }


    public function delete(Request $request,$id)
    {
    	$task = Task::find($id)->delete();
        return $this->sendResponse('Task deleted successfully');
    }

    public function search_tasks(Request $request)
    {
/*
        $task_view = Task::find($id) ;

        // Get task created and due dates
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->created_at);
        $to   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->duedate ); // add here the due date from create task

        $current_date = \Carbon\Carbon::now();
 
        // Format dates for Humans
        $formatted_from = $from->toRfc850String();  
        $formatted_to   = $to->toRfc850String();

        // Get Difference between current_date and duedate = days left to complete task
        // $diff_in_days = $from->diffInDays($to);
        $diff_in_days = $current_date->diffInDays($to);
*/

       

        $task_title = $request['task_title'];
        $completed = $request['completed'];
        $f_created_at = $request['f_created_at'];
        $t_created_at = $request['t_created_at'];
        $duedate = $request['duedate'];
        $tasks    = Task::where(function($q) use($task_title,$completed,$f_created_at,$duedate  ) {
          $q->orWhere('task_title', 'LIKE' , '%'.$task_title.'%')
            ->Where('completed' , 'LIKE' , '%'.$completed.'%')->Where('created_at' , 'LIKE' , '%'.$f_created_at.'%')->Where('duedate' , 'LIKE' , '%'.$duedate.'%');
        })->paginate($this->default_paginate_number);
        
        return $this->sendResponse(compact('tasks','keyword'));
    }
}