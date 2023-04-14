<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class ImageResource extends Model
{
    use HasFactory;

    
    public function imageable()
    {
        return $this->morphTo();
    }

    public function saveImage($imageFile, $model, $path=""){
        $dir = "public/images".$path;
        $name = $imageFile->getClientOriginalName();
        $imageId = uniqid().".".$imageFile->getClientOriginalExtension();
        $size = $imageFile->getSize();
        $path = $imageFile->storeAs($dir, $imageId);
        $imageModel = $model->image;
        if($imageModel){
            Storage::delete($imageModel->path);
            $imageModel->delete();
        }
        $this->filename = $name;
        $this->path = $path;
        $this->url = Storage::url($path);
        $this->image_id = $imageId;
        $this->size = $size;
        $this->imageable()->associate($model);
        $this->save();
        return $this;
    }
    public function saveTemp($request, $fieldname){
        $imageBank = $request->get($fieldname);
        $base64File = $request->input('file');        
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageBank["image"]));
        // save it to temporary dir first.
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $fileData);
        // this just to help us get file info.
        $tmpFile = new File($tmpFilePath);
        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $imageBank["name"],
            $imageBank["type"],
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );

        return $file;
    }
}
