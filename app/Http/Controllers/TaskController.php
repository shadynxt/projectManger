<?php
namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;

use App\Notifications\TaskNewNotification;
// import our models
use App\Project;
use App\Task;
use App\TaskFiles;
use App\User; 
use Illuminate\Support\Facades\Notification;
use StreamLab\StreamLabProvider\Facades\StreamLabFacades;

use Illuminate\Support\Facades\Input; 

class TaskController extends Controller
{
/*===============================================
    INDEX
===============================================*/
    public function index()
    {
        $users =  User::all() ; 
        $user = auth()->user();
        if($user->admin == 2){
           $tasks  = Task::orderBy('created_at', 'desc')->paginate(10); 
        }else{
            $tasks = Task::orderBy('created_at', 'desc')->where("user_id",$user->id)->paginate(10);
        }
        
         // Paginate Tasks 

        return view('task.tasks')->with('tasks', $tasks) 
                                 ->with('users', $users ) ;
                             
    }

/*===============================================
    LIST Tasks
===============================================*/
    public function tasklist( $projectid ) {

        // dd($projectid);
        $users =  User::all() ;
        $p_name = Project::find($projectid) ;
        // ->get()  will return a collection
        $task_list = Task::where('project_id','=' , $projectid)->get();
        return view('task.list')->with('users', $users) 
                                ->with('p_name', $p_name)
                                ->with('task_list', $task_list) ;
    }

/*===============================================
    VIEW Task
===============================================*/
    public function view($id)  {
        $images_set = [] ;
        $files_set = [] ;
        $images_array = ['png','gif','jpeg','jpg'] ;
        // get task file names with task_id number
        $taskfiles = TaskFiles::where('task_id', $id )->get() ;

        if ( count($taskfiles) > 0 ) { 
            foreach ( $taskfiles as $taskfile ) {

                // explode the filename into 2 parts: the filename and the extension
                $taskfile = explode(".", $taskfile->filename ) ;
                // store images only in one array
                // $taskfile[0] = filename
                // $taskfile[1] = jpg
                // check if extension is a image filetype
                if ( in_array($taskfile[1], $images_array ) ) 
                    $images_set[] = $taskfile[0] . '.' . $taskfile[1] ;
                    // if not an image, store in files array
                else
                    $files_set[] = $taskfile[0] . '.' . $taskfile[1]; 
            }
        }



        $task_view = Task::find($id) ;
        /*
        $user =auth()->user();
        //dd($user);
        if($user->role == 1){
            $task_view = Task::where('id','=',$id)->Where('user_id',$user->id)->first();
            if(!empty($task_view)){
                $task_view = Task::where('id',$id)->first();
                return view('task.view')->with('task', $task_view);
            }
            else
            {
                return redirect('/')->with('error','CSRF SUCKS! , You are not authorized to view that task');
            }
        }
        else{
            $task_view = Task::where('id',$id)->first();
            return view('task.view')->with('task_view', $task_view);
        }*/


        // Get task created and due dates
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->created_at);
        $to   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->duedate);

         $new_from   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->start_date);
         $new_ac_from   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->actual_start_date);
         $new_to   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->end_date);
         $new_ac_to   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_view->actual_end_date);
        $current_date = \Carbon\Carbon::now();
 
        // Format dates for Humans
        $formatted_from = $from->toRfc850String();  
        $formatted_to   = $to->toRfc850String();
        $formatted_new_from = $new_from->toRfc850String();
        $formatted_new_ac_from = $new_ac_from->toRfc850String();
        $formatted_new_to = $new_to->toRfc850String();
        $formatted_new_ac_to = $new_ac_to->toRfc850String();
        // Get Difference between current_date and duedate = days left to complete task
        // $diff_in_days = $from->diffInDays($to);
        $diff_in_days = $current_date->diffInDays($to);

        // Check for overdue tasks
        $is_overdue = ($current_date->gt($to) ) ? true : false ;

        
        // $task_view->project->project_name   will output the project name for this specific task
        // to populate the right sidebar with related tasks
        $projects = Project::all() ;
        return view('task.view')
            ->with('task_view', $task_view) 
            ->with('projects', $projects) 
            ->with('taskfiles', $taskfiles)
            ->with('diff_in_days', $diff_in_days )
            ->with('is_overdue', $is_overdue) 
            ->with('formatted_from', $formatted_from ) 
            ->with('formatted_to', $formatted_to )
            ->with('formatted_new_from', $formatted_new_from )
            ->with('formatted_new_ac_from', $formatted_new_ac_from )
            ->with('formatted_new_to', $formatted_new_to )
            ->with('formatted_new_ac_to', $formatted_new_ac_to )
            ->with('images_set', $images_set)
            ->with('files_set', $files_set) ;
    }

/*===============================================
    SORT TASKS
===============================================*/
    public function sort( $key ) {
        $users = User::all() ;
        // dd ($key) ; 
        switch($key) {
            case 'task':
                $tasks = Task::orderBy('task')->paginate(10); // replace get() with paginate()
            break;
            case 'priority':
                $tasks = Task::orderBy('priority')->paginate(10);
            break;
            case 'completed':
                $tasks = Task::orderBy('completed')->paginate(10);
            break;
        }

        return view('task.tasks')->with('users', $users)
                                ->with('tasks', $tasks) ;
    }

/*===============================================
    CREATE TASK
===============================================*/
    public function create()
    {
        $projects = Project::all()  ;
        $users = User::all() ;
        return view('task.create')->with('projects', $projects) 
                                  ->with('users', $users) ;        
    }

/*===============================================
    STORE NEW TASK
===============================================*/
    public function store(Request $request)
    {
        // dd($request->all() ) ;
        
        $tasks_count = Task::count() ;
        
        if ( $tasks_count < 200  ) { 
            // dd( $request->all()  ) ;
            // dd($request->file('photos'));

            $this->validate( $request, [
                'task_title' => 'required',
                'task'       => 'required',
                'project_id' => 'required|numeric',
                'photos.*'   => 'sometimes|required|mimes:png,gif,jpeg,jpg,txt,pdf,doc',  // photos is an array: photos.*
                'start_date'    => 'required',
                'actual_start_date'    => 'required',
                'end_date'    => 'required',
                'actual_end_date'    => 'required',
                'duedate'    => 'required'
            ]) ;

            // dd($request->all() ) ;
            // First save Task Info
            $task = Task::create([
                'project_id' => $request->project_id,
                'user_id'    => $request->user,
                'task_title' => $request->task_title,
                'task'       => $request->task,
                'priority'   => $request->priority,
                'start_date' => $request->start_date,
                'actual_start_date' => $request->actual_start_date,
                'end_date' => $request->end_date,
                'actual_end_date' => $request->actual_end_date,
                'duedate'    => $request->duedate
            ]);

            if($task){
            $user = User::all();
            Notification::send($user , new TaskNewNotification($task));
            $data = 'We Have New Task ' .$task->task_title ." <br> Added By " . auth()->user()->name." <br> Added At " . $task->created_at;
            StreamLabFacades::pushMessage('test' , 'AddTask' , $data);
        }

            // Then save files using the newly created ID above
            if( $request->hasFile('photos') ) {
                foreach ($request->photos as $file) {

                    $filename = strtr( pathinfo( time() . '_' . $file->getClientOriginalName(), PATHINFO_FILENAME) , [' ' => '', '.' => ''] ) . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                    $file->move('images',$filename);

                    // save to DB
                    TaskFiles::create([
                        'task_id'  => $task->id, // newly created ID
                        'filename' => $filename  // For Regular Public Images

                    ]);
                }
            }
    
            Session::flash('success', 'Task Created') ;
            return redirect()->route('task.show') ; 
        }
        
        else {
            Session::flash('info', 'Please delete some tasks, Demo max tasks: 200') ;
            return redirect()->route('task.show') ;         
        }

    }

/*===============================================
    MARK TASK AS COMPLETED
===============================================*/
    public function completed($id)
    {
        $task_complete = Task::find($id) ;
        $task_complete->completed = 1;
        $task_complete->save() ;
        return redirect()->back();
    }

/*===============================================
    EDIT TASK
===============================================*/
    public function edit($id)
    {
        // $task_list = Task::where('project_id','=' , $projectid)->get();
        $task = Task::find($id)  ; 
        $taskfiles = TaskFiles::where('task_id', '=', $id)->get() ;
        // dd($taskfiles) ;
        $projects = Project::all() ;
        $users = User::all() ;

        return view('task.edit')->with('task', $task)
                                ->with('projects', $projects ) 
                                ->with('users', $users)
                                ->with('taskfiles', $taskfiles);
    }

/*===============================================
    UPDATE TASK
===============================================*/
    public function update(Request $request, $id)
    {
        // dd( $request->all() ) ;
        $update_task = Task::find($id) ;

        $this->validate( $request, [
            'task_title' => 'required',
            'task'       => 'required',
            'project_id' => 'required|numeric',
            'photos.*'   => 'sometimes|required|mimes:png,gif,jpeg,jpg,txt,pdf,doc' // photos is an array: photos.*
        ]) ;

        $update_task->task_title = $request->task_title; 
        $update_task->task       = $request->task;
        $update_task->user_id    = $request->user_id;
        $update_task->project_id = $request->project_id;
        $update_task->priority   = $request->priority;
        $update_task->completed  = $request->completed;
        $update_task->start_date = $request->start_date;
        $update_task->actual_start_date = $request->actual_start_date;
        $update_task->end_date = $request->end_date;
        $update_task->actual_end_date = $request->actual_end_date;
        $update_task->duedate    = $request->duedate;

        if( $request->hasFile('photos') ) {
            foreach ($request->photos as $file) {
                // remove whitespaces and dots in filenames : [' ' => '', '.' => ''] 
                $filename = strtr( pathinfo( time() . '_' . $file->getClientOriginalName(), PATHINFO_FILENAME) , [' ' => '', '.' => ''] ) . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

                $file->move('images',$filename);

                // save to DB
                TaskFiles::create([
                    'task_id'  => $request->task_id,
                    'filename' => $filename  // For Regular Public Images
                ]);
            }        
        }

        $update_task->save() ;
        
        Session::flash('success', 'Task was sucessfully edited') ;
        return redirect()->route('task.show') ;
    }

/*===============================================
    DESTROY TASK
===============================================*/
    public function destroy($id)
    {
        $delete_task = Task::find($id) ;
        $delete_task->delete() ;
        Session::flash('success', 'Task was deleted') ;
        return redirect()->back();
    }

/*===============================================
    DELETE FILE
===============================================*/
    public function deleteFile($id) {
        $delete_file = TaskFiles::find($id) ;
        // remove  file from public directory
        unlink( public_path() . '/images/' . $delete_file->filename ) ;

        // delete entry from database
        $delete_file->delete() ;
        Session::flash('success', 'File Deleted') ;
        return redirect()->back(); 
    }

/*===============================================
    SEARCH TASK
===============================================*/
    public function searchTask()
    {
        $completed = Task::where('completed' ,'=',1)->get();
       
        $value = Input::get('task_search');
        // Search Inside the Contents of a task
        $tasks = Task::where('task_title', 'LIKE', '%' . $value . '%')->orWhere('completed', 'LIKE', '%' . $value . '%')->limit(25)->get();
        return view('task.search')->with('value', $value)
                                  ->with('tasks', $tasks) ;
    }

}
