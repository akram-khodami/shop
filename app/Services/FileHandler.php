<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileHandler
{
    protected $disk;

    public function __construct(string $disk = 'public')
    {
        $this->disk = $disk;
    }

    public function upload(UploadedFile $file, string $directory, string $filename = null): string
    {
        $filename = $filename ?: $file->hashName();

        return $file->storeAs($directory, $filename, $this->disk);
    }

    public function delete(string $path): bool
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {

            return Storage::disk($this->disk)->delete($path);

        }

        return false;
    }

    public function replace(UploadedFile $newFile, ?string $oldPath, string $directory, string $filename = null): string
    {
        $this->delete($oldPath);

        return $this->upload($newFile, $directory, $filename);
    }

    public function generateFilenameFromSlug($model, UploadedFile $file): string
    {
        return $model->slug . '.' . $file->getClientOriginalExtension();
    }


    //====================================================================================================
    private function MY_upload($request, $model, $input_name, $disk)
    {
        $image = $request->file($input_name);

        $image_name = $model->slug . '.' . $image->getClientOriginalExtension();

        //remove preview image
        if ($model->$input_name && Storage::disk($disk)->exists($model->$input_name)) {

            Storage::disk($disk)->delete($model->$input_name);

        }

        $path = $image->storeAs($model . 's', $image_name, $disk);

        return $path;
    }

}
