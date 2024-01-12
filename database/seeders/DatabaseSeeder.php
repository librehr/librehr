<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Request;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        Request::query()->create([
            'name' => 'absences'
        ]);

        Request::query()->create([
            'name' => 'signs'
        ]);

         \App\Models\User::factory()->create([
             'name' => 'borja',
             'email' => 'borja@librehr.com',
             'role_id' => Role::query()->create(['name' => 'admin'])->id,
             'password' => Hash::make('12345678'),
         ]);

        Role::query()->create(['name' => 'user']);
    }
}
