<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AbsenceType;
use App\Models\Business;
use App\Models\Calendar;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\DocumentsType;
use App\Models\Place;
use App\Models\Planning;
use App\Models\Post;
use App\Models\Request;
use App\Models\Role;
use App\Models\TasksCategory;
use App\Models\Team;
use App\Models\Tool;
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
        $businessUuid = Str::uuid();
        Role::query()->create(['name' => 'manager']);
        Role::query()->create(['name' => 'user']);

        Request::query()->create([
            'name' => 'absences'
        ]);

        Request::query()->create([
            'name' => 'signs'
        ]);

        Business::query()->create([
            'name' => 'Test Company',
            'uuid' => $businessUuid,
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

        $place = Place::query()->create([
            'name' => 'Alicante',
            'business_id' => 1,
        ]);

        Planning::query()->create([
            'name' => '8H Monday to Friday',
            'business_id' => 1,
            'attributes' => json_decode('{"periods": [{"date": "1-1 - 31-12", "work_days": [{"day": "1", "times": [{"to": "16:00:00", "from": "08:00:00"}]}, {"day": "2", "times": [{"to": "16:00:00", "from": "08:00:00"}]}, {"day": "3", "times": [{"to": "16:00:00", "from": "08:00:00"}]}, {"day": "4", "times": [{"to": "16:00:00", "from": "08:00:00"}]}, {"day": "5", "times": [{"to": "16:00:00", "from": "08:00:00"}]}]}]}', true)
        ]);

        $tools = [
            'Software Licence', 'Smartphone', 'Laptop'
        ];

        foreach ($tools as $tool) {
            Tool::query()->create([
                'name' => $tool
            ]);
        }

        $contracTypes = ['Software Developer', 'Human Resources', 'Sales', 'Customer Support'];
        foreach ($contracTypes as $type) {
            ContractType::query()->create(['name' => $type, 'attributes' => []]);
        }

        DocumentsType::query()->create([
            'name' => 'Model 147 PDF'
        ]);

        DocumentsType::query()->create([
            'name' => 'Authorization PDF'
        ]);

        AbsenceType::create([
            'name' => 'Holidays',
            'attributes' => json_decode('{"color": {"text": "#ffffff", "background": "#62a36e"}, "attachments": false, "is_holidays": true}', true)
        ]);

        AbsenceType::create([
            'name' => 'Sick leave',
            'attributes' => json_decode('{"color": {"text": "#ffffff", "background": "#ff0000"}, "attachments": true, "is_holidays": false}', true)
        ]);

        Post::query()->create([
            'business_id' => 1,
            'title' => 'Welcome to LibreHR',
            'body' => 'LibreHR includes features for multi-company management, time and absence tracking, office seat reservation, and task, tool, expense, document, and team management.'
        ]);

        Contract::factory(20)->create();

        TasksCategory::query()->create([
            'name' => 'Test project',
            'business_id' => 1,
        ]);

        \App\Models\User::query()->where('id', 1)->update([
            'name' => 'demo',
            'email' => 'demo@librehr.com',
            'role_id' => Role::query()->create(['name' => 'admin'])->id,
            'password' => Hash::make('12345678'),
            'attributes' => [
                'default_business' => $businessUuid
            ],
            'active' => true,
        ]);

        \Artisan::call('cache:clear');
    }
}
