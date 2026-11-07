<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LogoUploadSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('alamat')->nullable();
            $table->timestamps();
        });
        Storage::fake('public');
    }

    public function test_valid_logo_keeps_its_unique_name(): void
    {
        Storage::disk('public')->put('logo/unique.png', $this->png());
        $setting = Setting::create(['logo' => 'logo/unique.png']);
        $this->assertSame('logo/unique.png', $setting->fresh()->logo);
        Storage::disk('public')->assertExists('logo/unique.png');
    }

    public function test_script_disguised_as_image_is_rejected(): void
    {
        Storage::disk('public')->put('logo/attack.png', '<?php echo "unsafe";');
        $this->expectException(ValidationException::class);
        Setting::create(['logo' => 'logo/attack.png']);
    }

    public function test_path_outside_logo_directory_is_rejected_without_moving_it(): void
    {
        Storage::disk('public')->put('other.png', $this->png());
        try {
            Setting::create(['logo' => 'logo/../other.png']);
            $this->fail('Unsafe path was accepted.');
        } catch (ValidationException) {
            Storage::disk('public')->assertExists('other.png');
            $this->assertSame(0, Setting::count());
        }
    }

    public function test_image_with_php_extension_is_rejected(): void
    {
        Storage::disk('public')->put('logo/attack.php', $this->png());
        $this->expectException(ValidationException::class);
        Setting::create(['logo' => 'logo/attack.php']);
    }

    public function test_updating_settings_preserves_legacy_logo(): void
    {
        Storage::disk('public')->put('logo.png', $this->png());
        $id = \Illuminate\Support\Facades\DB::table('settings')->insertGetId(['logo' => 'logo.png']);
        $setting = Setting::findOrFail($id);
        $setting->update(['alamat' => 'Updated']);
        $this->assertSame('logo.png', $setting->fresh()->logo);
        Storage::disk('public')->assertExists('logo.png');
    }

    public function test_settings_form_saves_a_real_upload_with_a_server_generated_name(): void
    {
        Storage::fake('upload-temporary');
        \Livewire\Livewire::test(\App\Filament\Pages\Settings::class)
            ->fillForm(['logo' => \Illuminate\Http\UploadedFile::fake()->createWithContent('client.png', $this->png())])
            ->call('save')
            ->assertHasNoFormErrors();

        $path = Setting::firstOrFail()->logo;
        $this->assertMatchesRegularExpression('/\Alogo\/[0-9a-f-]{36}\.png\z/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_temporary_upload_endpoint_rejects_php_extension(): void
    {
        Storage::fake('upload-temporary');
        // Livewire forces its own temporary disk in testing mode.
        $this->app->detectEnvironment(fn () => 'local');
        $this->withSession(['_token' => 'upload-test-token']);
        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute('livewire.upload-file', now()->addMinutes(5), [], false);
        $this->withHeader('Accept', 'application/json')->post($url, ['_token' => 'upload-test-token', 'files' => [\Illuminate\Http\UploadedFile::fake()->createWithContent('image.php', $this->png())]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('files.0');
        $this->assertSame([], Storage::disk('upload-temporary')->allFiles());
    }

    public function test_temporary_upload_endpoint_stores_images_on_private_disk(): void
    {
        Storage::fake('upload-temporary');
        // Livewire forces its own temporary disk in testing mode.
        $this->app->detectEnvironment(fn () => 'local');
        $this->withSession(['_token' => 'upload-test-token']);
        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute('livewire.upload-file', now()->addMinutes(5), [], false);
        $this->withHeader('Accept', 'application/json')->post($url, ['_token' => 'upload-test-token', 'files' => [\Illuminate\Http\UploadedFile::fake()->createWithContent('image.png', $this->png())]])
            ->assertOk();
        $this->assertCount(1, Storage::disk('upload-temporary')->allFiles());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_oversized_logo_is_rejected(): void
    {
        Storage::disk('public')->put('logo/large.png', $this->png() . str_repeat('x', 2048 * 1024));
        $this->expectException(ValidationException::class);
        Setting::create(['logo' => 'logo/large.png']);
    }

    public function test_replacing_logo_preserves_old_file_and_new_unique_path(): void
    {
        Storage::disk('public')->put('logo/first.png', $this->png());
        Storage::disk('public')->put('logo/second.png', $this->png());
        $setting = Setting::create(['logo' => 'logo/first.png']);
        $setting->update(['logo' => 'logo/second.png']);
        $this->assertSame('logo/second.png', $setting->fresh()->logo);
        Storage::disk('public')->assertExists(['logo/first.png', 'logo/second.png']);
    }
    private function png(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
    }
}