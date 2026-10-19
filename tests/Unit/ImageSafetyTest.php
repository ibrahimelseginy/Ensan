<?php

namespace Tests\Unit;

use App\Http\Controllers\PublicStorageController;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ImageSafetyTest extends TestCase
{
    public function test_failed_image_validation_preserves_previous_file(): void
    {
        Storage::fake('uploads');
        Storage::disk('uploads')->put('pictures/old.webp', 'old image');
        $file = UploadedFile::fake()->createWithContent('unsafe.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $this->assertNull(app(ImageUploadService::class)->upload($file, 'pictures', 'pictures/old.webp'));
        Storage::disk('uploads')->assertExists('pictures/old.webp');
    }

    public function test_upload_converts_image_and_honors_maximum_dimensions(): void
    {
        Storage::fake('uploads');
        Storage::disk('uploads')->put('pictures/old.webp', 'old image');
        $path = app(ImageUploadService::class)->upload(UploadedFile::fake()->image('photo.png', 200, 100), 'pictures', 'pictures/old.webp', 80, 80);
        $this->assertNotNull($path);
        $this->assertStringEndsWith('.webp', $path);
        $info = getimagesize(Storage::disk('uploads')->path($path));
        $this->assertSame([80, 40], [$info[0], $info[1]]);
        Storage::disk('uploads')->assertMissing('pictures/old.webp');
    }

    public function test_public_fallback_rejects_traversal(): void
    {
        $this->expectException(HttpException::class);
        (new PublicStorageController)->show('../../../composer.json');
    }

    public function test_public_fallback_never_serves_private_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('local')->put('document.txt', 'private');
        $this->expectException(HttpException::class);
        (new PublicStorageController)->show('document.txt');
    }

    public function test_storage_route_points_to_public_fallback(): void
    {
        $route = app('router')->getRoutes()->match(\Illuminate\Http\Request::create('/storage/example.webp'));
        $this->assertSame(PublicStorageController::class.'@show', $route->getActionName());
    }
}
