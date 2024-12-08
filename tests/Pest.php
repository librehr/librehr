<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Models\AbsenceType;
use App\Models\Business;
use App\Models\Calendar;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Desk;
use App\Models\DocumentsType;
use App\Models\Floor;
use App\Models\Place;
use App\Models\Planning;
use App\Models\Post;
use App\Models\Request;
use App\Models\Role;
use App\Models\Room;
use App\Models\TasksCategory;
use App\Models\Team;
use App\Models\Tool;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

dataset(
    'user_contract_resources',
    [
        \App\Filament\App\Pages\TimeOff::class,
        \App\Filament\App\Pages\Attendances::class,
        \App\Filament\App\Pages\Requests::class,
    ]
);

dataset(
    'profile_resources',
    [
        \App\Filament\App\Pages\Dashboard::class,
        \App\Filament\App\Pages\MyProfile\Profile::class,
        \App\Filament\App\Pages\MyProfile\ProfileContracts::class,
        \App\Filament\App\Pages\MyProfile\ProfileTools::class,
        \App\Filament\App\Pages\MyProfile\Documents::class,
    ]
);

dataset(
    'manager_resources',
    [
        \App\Filament\App\Resources\AbsenceResource::class,
        \App\Filament\App\Resources\ContractResource::class,
        \App\Filament\Admin\Resources\UserResource::class,
        \App\Filament\App\Pages\HumanResources\AttendancesControl::class,
        \App\Filament\App\Pages\HumanResources\TimeOffControl::class,
    ]
);

dataset(
    'business_resources',
    [
        \App\Filament\App\Resources\CalendarResource::class,
        \App\Filament\App\Resources\PostResource::class,
    ]
);

dataset(
    'administration_resources',
    [
        \App\Filament\Admin\Resources\AbsenceTypeResource::class,
        \App\Filament\Admin\Resources\BusinessResource::class,
        \App\Filament\Admin\Resources\ContractTypeResource::class,
        \App\Filament\Admin\Resources\DocumentsTypeResource::class,
        \App\Filament\Admin\Resources\RoleResource::class,
        \App\Filament\Admin\Resources\TasksCategoryResource::class,
        \App\Filament\Admin\Resources\ToolResource::class,
        \App\Filament\App\Resources\PlanningResource::class,
    ]
);

uses(
    Tests\TestCase::class,
    // Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/
function login($type = 'user')
{
    test()->actingAs(\App\Models\User::factory()->create(
        ['role_id' => \App\Models\Role::factory()->create([
            'name' => $type
        ])]
    ));
}

function loginWithContract($type = 'user')
{
    $user = \App\Models\User::factory()->create(
        ['role_id' => \App\Models\Role::factory()->create([
            'name' => $type
        ])]
    );

    $contract = \App\Models\Contract::factory()->create(
        [
            'user_id' => $user->id
        ]
    );

    test()->actingAs($contract->user);
}

function loadDatabase()
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

    $floor = Floor::query()->create([
        'name' => 'First Floor',
        'business_id' => 1,
        'place_id' => $place->id,
    ]);

    file_put_contents(storage_path('app/public/demo-map.png'), file_get_contents(asset('images/map.png')));

    $room = Room::query()->create([
        'name' => 'The great hall',
        'business_id' => 1,
        'attributes' => [
            'image' => 'demo-map.png',
        ],
        'floor_id' => $floor->id,
    ]);

    $desks = range(1, 30);
    foreach ($desks as $desk) {
        Desk::query()->create([
            'room_id' => $room->id,
            'business_id' => 1,
            'name' => 'A' . $desk,
            'attributes' => [],
            'active' => true,
        ]);
    }

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
}
function something()
{
    // ..
}
