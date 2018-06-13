<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;
use Image;
use Illuminate\Http\UploadedFile;

class TaskFiles extends Model
{
    protected $fillable = ['task_id', 'filename'];
    
    public function task()
    {
        return $this->belongsTo('App\Task');
    }

    public static function uploadFile($file, $user, $path='public', $extension = 'jpg', $type = 'image')
    {
        if (is_file($file)) {
            if(substr($file->getMimeType(), 0, 5) == 'image') {
            // $savedFile = $file->store($path);
            $savedFile;
            $file_name = $type . '_' . date('YmdHis'). '_' . time() . '.' . $extension;

            $img = Image::make($file);

            $fileSize = ($file->getClientSize()/1024);
            if($fileSize > 1000)
            {
              $savedFile =  $img->save('public/'.$file_name,20);
            }else{
              $savedFile =  $img->save('public/'.$file_name,50);
            }
            $image = $savedFile;
            $saved_image_uri = $savedFile->dirname.'/'.$savedFile->basename;
            $savedFile = new UploadedFile($saved_image_uri, 'tmp.'.$savedFile->extension, $savedFile->mime,null,null,true);
            $savedFile = $savedFile->store($path);
            $image->destroy();
          }else{
            $savedFile = $file->store($path);
          }

            $fileModel = self::create([
                'user_id'    => $user->id,
                'name'       => $file->getClientOriginalName(),
                'extension'  => $file->getClientOriginalExtension(),
                'local_path' => $savedFile,
                'url'        => Storage::url($savedFile),
                'file_size'  => Storage::size($savedFile),
                'is_active'  => (Storage::getVisibility($savedFile) == 'public') ? 1 : 0,
            ]);
            @unlink($saved_image_uri);
        } else {
            //generating unique file name
            $file_name = $type . '_' . date('YmdHis'). '_' . time() . '.' . $extension;
            if ($file != "") {
                if($path != 'public') { // $path != 'local'
                    $savedFile = Storage::disk('public')->put($file_name, base64_decode($file));
                    Storage::move('public/'.$file_name, $path.'/'.$file_name);
                } else {
                    $savedFile = Storage::disk($path)->put($file_name, base64_decode($file));
                }

                $fileModel = self::create([
                    'user_id'    => $user->id,
                    'name'       => $file_name,
                    'extension'  => $extension,
                    'local_path' => ($path != 'public' || $path != 'local') ? $path.'/'.$file_name : 'public/'.$file_name,
                    'url'        => ($path != 'public' || $path != 'local') ? Storage::url($path.'/'.$file_name) : Storage::url($file_name),
                    'file_size'  => 6000,
                    'is_active'  => 1
                ]);
            }
        }

        return $fileModel;
    }

}
