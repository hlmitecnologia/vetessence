<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

trait HasPhoto
{
    public function getPhotoUrlAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    public function savePhoto(UploadedFile $file, $folder = 'photos')
    {
        if ($this->photo) {
            $this->deletePhoto();
        }
        
        $filename = $folder . '/' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public');
        
        $this->photo = $path;
        $this->save();
        
        return $path;
    }

    public function deletePhoto()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            Storage::disk('public')->delete($this->photo);
        }
        $this->photo = null;
        $this->save();
    }
}
