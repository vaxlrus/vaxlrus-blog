<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class RedisService
{
    /**
     * Установить текущую дату
     *
     * @param ?Carbon $today
     * @return void
     */
    public function setCurrentDate(?Carbon $today): void
    {
        $key = "app:current_date";

        if (! $today) {
            $today = Carbon::now();
        }

        Redis::set($key, $today->toDayDateTimeString());
    }

    /**
     * Получить текущую дату
     *
     * @return Carbon|null
     */
    public function getCurrentDate(): ?Carbon
    {
        $key = "app:current_date";

        if (! Redis::get($key)) {
            return null;
        }

        return Carbon::createFromTimeString(Redis::get($key));
    }
}
