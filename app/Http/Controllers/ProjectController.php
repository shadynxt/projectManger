<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Notifications\ProjectNewNotification;
use App\Project;
use App\Task;
use App\User;
use Illuminate\Support\Facades\Notification;
use StreamLab\StreamLabProvider\Facades\StreamLabFacades;
use Illuminate\Support\Facades\Input; 

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index()
    {
        // $tasks = Task::
        $projects = Project::all() ;
        return view('project.projects')->with('projects', $projects) ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $proje
        return view('project.create') ;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $projects_count = Project::count() ;
      
        if ( $projects_count < 1000  ) {  
            
            // dd( $request->all()  ) ;
            $this->validate( $request, [
                'project' => 'required'
            ] ) ;        
    
            $project_new = new Project;
            $project_new->project_name = $request->project;
            $project_new->start_date = $request->start_date;
            $project_new->end_date = $request->end_date;
            if( $project_new->save() ){

                $user = User::all();
                 Notification::send($user , new ProjectNewNotification($project_new));
                $data = 'We Have New Project ' .$project_new->project_name ." <br> Added By " . auth()->user()->name." <br> Added At " . $project_new->created_at;
                StreamLabFacades::pushMessage('test' , 'AddProject' , $data);
            }
            Session::flash('success', 'Project Created') ;
            return redirect()->route('project.show') ;
        }
        
        else {
            Session::flash('info', 'Please delete some projects, Demo max: 1000') ;
            return redirect()->route('project.show') ;          
        }
    }




     public function view($id)  {
       



        $project_view = Project::find($id) ;

        // Get task created and due dates
       // $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project_view->created_at);
        

         $new_from   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project_view->start_date);
         
         $new_to   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $project_view->end_date);
        
        $current_date = \Carbon\Carbon::now();
 
        // Format dates for Humans
        //$formatted_from = $from->toRfc850String();  
        
        $formatted_new_from = $new_from->toRfc850String();
        
        $formatted_new_to = $new_to->toRfc850String();
        
        // Get Difference between current_date and duedate = days left to complete task
        // $diff_in_days = $from->diffInDays($to);
        $diff_in_days = $current_date->diffInDays($to);

        // Check for overdue tasks
        $is_overdue = ($current_date->gt($to) ) ? true : false ;

        // $task_view->project->project_name   will output the project name for this specific task
        // to populate the right sidebar with related tasks
        $task_view = Task::all() ;
        return view('project.view')
            ->with('project_view', $project_view) 
            
            
            ->with('diff_in_days', $diff_in_days )
            ->with('is_overdue', $is_overdue) 
            ->with('task_view', $task_view ) 
            
            ->with('formatted_new_from', $formatted_new_from )
            
            ->with('formatted_new_to', $formatted_new_to );
            
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit_project =  Project::find($id) ;
        return view('project.edit')->with('edit_project', $edit_project)  ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update_project = Project::find($id) ;
        $update_project->project_name = $request->name;
        $update_project->start_date = $request->start_date;
        $update_project->end_date = $request->end_date;
        $update_project->save() ;
        Session::flash('success', 'Project was sucessfully edited') ;
        return redirect()->route('project.show') ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete_project = Project::find($id) ;
        $delete_project->delete() ;
        Session::flash('success', 'Project was deleted and tasks associated with it') ;
        return redirect()->back();        
        
    }

    public function searchProject()
    {
        
       
        $value = Input::get('project_search');
        // Search Inside the Contents of a task
        $projects = Project::where('project_name', 'LIKE', '%' . $value . '%')->limit(25)->get();
        return view('project.search')->with('value', $value)
                                  ->with('projects', $projects) ;
    }

}
