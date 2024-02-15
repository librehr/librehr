<?php

namespace App\Models\Sushi;

use App\Models\Contract;
use App\Models\User;
use App\Services\Attendances;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AttendanceControl extends Model
{
    use \Sushi\Sushi;


    public static $date;
    public static $businessId;
    public static $test;

    protected $connection = 'default';

    protected $casts = [
        'summary' => 'array'
    ];

    public static function setDateBusiness($date, $business)
    {
        self::$date = $date;
        self::$businessId = $business;
        return self::query();
    }

    public function getRows()
    {
        $users = User::query()->with(['contracts' => function ($query) {
            $query->where('business_id', self::$businessId)
            ->where('end', null)
            ->with(['team', 'attendancesValidations' => function ($query) {
                    $query->whereYear('date', Carbon::parse(self::$date));
                    $query->whereMonth('date', Carbon::parse(self::$date));
            }]);
        }])->get();

        [$attendances, $summary] = app(Attendances::class)->buildSingleContractAttendances(
            self::$date,
            $users
        );



        $attendances = collect($summary)->map(function ($attendances, $key) {
            $row['id'] = $key;
            $row['contract_id'] = $key;
            $row['date'] = self::$date;
            $row['summary'] = json_encode($attendances);
            return $row;
        })->toArray();

        return array_values($attendances);
    }

    protected function newRelatedInstance($class)
    {
        return tap(new $class, function ($instance) use ($class) {
            if (!$instance->getConnectionName()) {
                $instance->setConnection($this->getConnectionResolver()->getDefaultConnection());
                parent::newRelatedInstance($class);
            }
        });
    }
}
