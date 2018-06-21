<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = [ 
    	'project_name','start_date', 'end_date'
    ] ;


    public function tasks() {
    	return $this->hasMany('App\Task');
    }

     public static $rules = [
            'project_name' => 'required',
            
            'start_date' => 'required',
            
            'end_date' => 'required',
            

    ];
    
     public static $messages =[
       'project_name.required'            =>  'هذا الحقل مطلوب',
     
      'start_date.required'             =>  'هذا الحقل مطلوب',
      
      'end_date.required'             =>  'هذا الحقل مطلوب',
      
    ];


}
