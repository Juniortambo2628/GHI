<?php

namespace App\Http\Controllers\Admin\Concerns;

trait HasStatusOptions
{
    protected function getStatusOptions(string $modelClass): array
    {
        return $modelClass::select('status')->distinct()->orderBy('status')
            ->pluck('status')
            ->map(fn ($s) => ['value' => $s, 'label' => ucfirst($s)])
            ->values()
            ->all();
    }
}
