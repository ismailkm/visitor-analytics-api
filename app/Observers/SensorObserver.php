<?php

namespace App\Observers;

use App\Models\Sensor;
use Illuminate\Support\Facades\Cache;

class SensorObserver
{

    protected function clearCache(): void
    {
        Cache::tags(['sensors'])->flush();
        Cache::forget('app:summary');
    }

    /**
     * Handle the Sensor "created" event.
     */
    public function created(Sensor $sensor): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Sensor "updated" event.
     */
    public function updated(Sensor $sensor): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Sensor "deleted" event.
     */
    public function deleted(Sensor $sensor): void
    {
        $this->clearCache();
    }
}
