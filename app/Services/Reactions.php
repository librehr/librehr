<?php

namespace App\Services;


use App\Models\Attendance;
use App\Models\AttendanceValidation;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;

class Reactions extends BaseService
{
    public function addReaction($attributes, $userId, $type)
    {
        $user = User::query()->find($userId)->first();
        $added = true;
        $myReaction = $attributes['reactions'][$type][$userId] ?? null;
        if (!empty($myReaction)) {
            $added = false;
            unset($attributes['reactions'][$type][$userId]);
        } else {
            $attributes['reactions'][$type][$userId] = data_get($user, 'name');
        }

        return [$attributes, $added];
    }

    /**
     * @param $type
     * @return string
     */
    public function parseReactionNames($type): string
    {
        return match ($type) {
            'check' => 'Ok Icon',
            'face-smile' => 'Happy Smile',
            'face-frown' => 'Sad Smile',
            'rocket-launch' => 'Rocket Icon',
        };
    }
}
