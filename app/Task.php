<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [ 
    	'project_id','user_id', 'task_title', 'task' , 'priority', 'duedate','photos','start_date','actual_start_date','end_date','actual_end_date'
     ] ;

     
     public function project() {

     	return $this->belongsTo('App\Project') ;
     }

     public function user() {

         return $this->belongsTo('App\User') ;
     }

     public function taskfiles() {

         return $this->hasMany('App\TaskFiles') ;
     }

    public static $rules = [
            'project_id' => 'required',
            'task_title' => 'required',
            'task_desc' => 'required',
            'task' => 'required',
            'priority' => 'required',
            'completed' => 'required',
            'start_date' => 'required',
            'actual_start_date' => 'required',
            'end_date' => 'required',
            'actual_end_date' => 'required',
            'duedate' => 'required',

    ];
    
     public static $messages =[
        'project_id.required'            =>  'هذا الحقل مطلوب',
      'task_title.required'            =>  'هذا الحقل مطلوب',
     
      'task_desc.required'             =>  'هذا الحقل مطلوب',
      'task.required'             =>  'هذا الحقل مطلوب',
      'priority.required'             =>  'هذا الحقل مطلوب',
      'completed.required'             =>  'هذا الحقل مطلوب',
      'start_date.required'             =>  'هذا الحقل مطلوب',
      'actual_start_date.required'             =>  'هذا الحقل مطلوب',
      'end_date.required'             =>  'هذا الحقل مطلوب',
      'actual_end_date.required'             =>  'هذا الحقل مطلوب',
      'duedate.required'             =>  'هذا الحقل مطلوب',
    ];
      
      
}
