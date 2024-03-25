<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Business;
use App\Models\Calendar;
use App\Models\Request;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::query()->create(['name' => 'manager']);
        Role::query()->create(['name' => 'user']);

        \App\Models\User::factory(120)->create([
            'password' => Hash::make('12345678')
        ]);

        Request::query()->create([
            'name' => 'absences'
        ]);

        Request::query()->create([
            'name' => 'signs'
        ]);

         \App\Models\User::factory()->create([
             'name' => 'demo',
             'email' => 'demo@librehr.com',
             'role_id' => Role::query()->create(['name' => 'admin'])->id,
             'password' => Hash::make('12345678'),
             'active' => true,
         ]);

        Business::query()->create([
            'name' => 'Test Company',
            'uuid' => Str::uuid(),
            'attributes' => [
                'default_currency' => config('librehr.currenct'),
                'default_vacations' => config('librehr.vacations'),
                'default_timezone' => config('librehr.timezone')
            ],
        ]);

        Calendar::factory(10)->create();


        Team::query()->create([
            'name' => 'Backend',
            'business_id' => 1,
        ]);

        Team::query()->create([
            'name' => 'Frontend',
            'business_id' => 1,
        ]);


        \Artisan::call('optimize');
    }
}
