<?php

namespace App\Observers;

use App\Models\Visitor;
use Illuminate\Support\Facades\Cache;

class VisitorObserver
{
    protected function clearCache(): void
    {
        Cache::tags(['visitors'])->flush();
         Cache::forget('app:summary');
    }

    /**
     * Handle the Visitor "created" event.
     */
    public function created(Visitor $visitor): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Visitor "updated" event.
     */
    public function updated(Visitor $visitor): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Visitor "deleted" event.
     */
    public function deleted(Visitor $visitor): void
    {
        $this->clearCache();
    }
}
