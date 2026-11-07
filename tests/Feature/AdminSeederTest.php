<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    public function test_admin_password_is_hashed_and_seeding_can_be_repeated(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'bumijaya@gmail.com')->firstOrFail();
        $this->assertTrue(Hash::check('tokobumijaya111', $admin->password));
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(1, User::count());
        $this->assertTrue(Hash::check('tokobumijaya111', $admin->fresh()->password));
    }

    public function test_seeding_updates_the_existing_admin(): void
    {
        $admin = User::create(['name' => 'admin', 'email' => 'bumijaya@gmail.com', 'password' => 'previous-password']);
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(1, User::count());
        $this->assertTrue(Hash::check('tokobumijaya111', $admin->fresh()->password));
    }
}
