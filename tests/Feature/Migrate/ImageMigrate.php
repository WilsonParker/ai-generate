<?php

namespace Tests\Feature\Migrate;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Prompt\Prompt;
use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Image\Models\Image;
use App\Services\Image\Models\Media;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImageMigrate extends TestCase
{
    public function test_migrate_image_to_media(): void
    {
        Image::all()->each(function (Image $image) {
            $media = new Media();
            $media->model_type = $image->imageable_type;
            $media->model_id = $image->imageable_id;
            $media->mime_type = $image->mime;
            $media->size = $image->size;
            $media->file_name = $image->name;
            $media->name = $image->origin_name;
            $media->collection_name = $image->imageable_type == 'user-information' ? 'avatar' : 'gallery';
            $media->disk = 'media';
            $media->conversions_disk = 'media';
            $media->manipulations = [];
            $media->custom_properties = [];
            $media->generated_conversions = [];
            $media->responsice_images = [];
            // $media->save();
        });
    }

    public function test_migrate_prompt_image_to_media(): void
    {
        $service = app()->make(ImageServiceContract::class);
        Prompt::whereHas('imageModels')->each(function (Prompt $prompt) use ($service) {
            $prompt->imageModels->each(function ($image) use ($service, $prompt) {
                $path = '/ai_generate/' . $image->path . '/' . $image->name;
                // dd($this->getS3FileAsUploadedFile($path));
                $s3File = Storage::disk('s3')->get($path);
                if (isset($s3File)) {
                    $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $s3File));
                    // save it to temporary dir first.
                    $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
                    file_put_contents($tmpFilePath, $fileData);

                    // this just to help us get file info.
                    $tmpFile = new File($tmpFilePath);

                    $file = new UploadedFile(
                        $tmpFile->getPathname(),
                        $tmpFile->getFilename(),
                        $tmpFile->getMimeType(),
                        0,
                        true, // Mark it as test, since the file isn't from real HTTP POST.
                    );
                    $file->store('avatars');
                    // $service->upload($prompt, $file, 'gallery');
                    exit;
                }
            });
        });
    }

    public function getS3FileAsUploadedFile(string $s3FilePath): UploadedFile
    {
        $s3Disk = Storage::disk('s3'); // Replace 's3' with your disk name

        // Download the file from S3 to a local temporary path
        $tempPath = tempnam(sys_get_temp_dir(), 's3file');
        $s3Disk->get($s3FilePath, $tempPath);

        // Create an UploadedFile instance from the local temporary file
        $uploadedFile = new UploadedFile(
            $tempPath,
            $s3Disk->name($s3FilePath),     // Use the original filename from S3
            $s3Disk->mimeType($s3FilePath), // Use the MIME type from S3
            null,
            true, // Delete the temporary file after it's used
        );

        return $uploadedFile;
    }
}
