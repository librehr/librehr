<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Attendance
 *
 * @property int $id
 * @property int $contract_id
 * @property \Illuminate\Support\Carbon $date
 * @property array|null $attributes
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon|null $end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contract $contract
 * @property-read mixed $start_format
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Attendance extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'date' => 'date',
        'start' => 'datetime',
        'end' => 'datetime',
        'attributes' => 'array',
    ];

    protected $appends = [
        'StartFormat'
    ];

    public function getStartFormatAttribute()
    {

        $diffInHours = now()->diffInHours($this->start);
        $diffInMinutes = now()->diffInMinutes($this->start) % 60;
        $diffInMinutesFormatted = str_pad($diffInMinutes, 2, '0', STR_PAD_LEFT);


        return "{$diffInHours}h {$diffInMinutesFormatted}m";
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public static function getCurrentAttendance()
    {
        $user = Auth::user();
        $activeContract = $user->getActiveContractId();
        // Check if there is something openm just close it
        $attendance = Attendance::query()
            ->where('contract_id', $activeContract)
            ->whereNull('end')
            ->first();

        return [
            $user, $activeContract, $attendance
        ];
    }
}
