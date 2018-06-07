<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\AppBaseController;
use App\User;
use App\Task;
use App\Project;
use Illuminate\Http\Request;
use App\Http\Requests\API\ApiTodoRequest;
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
    public function create(Request $request){
    	//$services = Service::find($request->input('id'));
        $input = $request->all();
        $user = auth()->user();
        if (!$user) {
            return $this->sendError('Please login !', 401);
        }
        $task = new Task();
		$task->user_id = $user->id;
        $task->project_id = $request->input('project_id');
        $task->task_title = $request->input('task_title');
        $task->task = $request->input('task_desc');
        $task->priority = $request->input('priority');
        $task->completed = $request->input('completed');
        $task->duedate = $request->input('duedate');
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
}