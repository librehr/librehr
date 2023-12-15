<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
