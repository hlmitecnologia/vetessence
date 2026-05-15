<?php

namespace Tests\Unit\Traits;

use App\Traits\HasPhoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoTestModel extends Model
{
    use HasPhoto;

    protected $table = '_test_photos';

    public $photo;

    protected $fillable = ['photo'];

    public $timestamps = false;

    public function save(array $options = []): bool
    {
        return true;
    }
}

class HasPhotoTest extends TestCase
{
    use DatabaseTransactions;

    public function test_save_photo_stores_file_and_updates_attribute(): void
    {
        Storage::fake('public');

        $model = new PhotoTestModel();
        $file = UploadedFile::fake()->image('pet.jpg');

        $path = $model->savePhoto($file, 'test_photos');

        Storage::disk('public')->assertExists($path);
        $this->assertEquals($path, $model->photo);
    }

    public function test_get_photo_url_returns_null_when_no_photo(): void
    {
        $model = new PhotoTestModel();
        $model->photo = null;

        $this->assertNull($model->getPhotoUrlAttribute());
    }

    public function test_delete_photo_removes_file_and_resets_attribute(): void
    {
        Storage::fake('public');

        $model = new PhotoTestModel();
        $file = UploadedFile::fake()->image('pet.jpg');
        $path = $model->savePhoto($file, 'test_photos');

        $model->deletePhoto();

        Storage::disk('public')->assertMissing($path);
        $this->assertNull($model->photo);
    }
}
