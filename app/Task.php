<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [ 
    	'project_id','user_id', 'task_title', 'task' , 'priority', 'duedate','photos'
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
            'duedate' => 'required',
    ];
    
     public static $messages =[
        'project_id.required'            =>  'هذا الحقل مطلوب',
      'task_title.required'            =>  'هذا الحقل مطلوب',
     
      'task_desc.required'             =>  'هذا الحقل مطلوب',
      'task.required'             =>  'هذا الحقل مطلوب',
      'priority.required'             =>  'هذا الحقل مطلوب',
      'completed.required'             =>  'هذا الحقل مطلوب',
      'duedate.required'             =>  'هذا الحقل مطلوب',
    ];
      
      
}
