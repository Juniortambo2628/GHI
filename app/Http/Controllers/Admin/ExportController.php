<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use App\Models\Cause;
use App\Models\Initiative;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\Story;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function contacts(Request $request): StreamedResponse
    {
        return $this->csv('contacts.csv', ['Name', 'Email', 'Subject', 'Status', 'Created'], ContactSubmission::query()->when($request->status, fn ($q, $status) => $q->where('status', $status))->latest('created_at')->cursor(), fn ($item) => [$item->full_name, $item->email, $item->subject, $item->status, $item->created_at]);
    }

    public function subscribers(Request $request): StreamedResponse
    {
        return $this->csv('subscribers.csv', ['Name', 'Email', 'Status', 'Subscribed'], NewsletterSubscriber::query()->when($request->status, fn ($q, $status) => $q->where('status', $status))->latest('subscribed_at')->cursor(), fn ($item) => [$item->name, $item->email, $item->status, $item->subscribed_at]);
    }

    public function content(Request $request, string $resource): StreamedResponse
    {
        $definitions = [
            'causes' => [Cause::class, 'Causes', ['Title', 'Status'], fn ($item) => [$item->title, $item->status]],
            'initiatives' => [Initiative::class, 'Initiatives', ['Title', 'Category', 'Status'], fn ($item) => [$item->title, $item->category, $item->status]],
            'events' => [Event::class, 'Events', ['Title', 'Date', 'Location', 'Status'], fn ($item) => [$item->title, $item->event_date, $item->location, $item->status]],
            'impact' => [ImpactActivity::class, 'Impact', ['Title', 'Date', 'People Affected', 'Status'], fn ($item) => [$item->title, $item->activity_date, $item->people_affected, $item->status]],
            'stories' => [Story::class, 'Stories', ['Title', 'Author', 'Category', 'Status'], fn ($item) => [$item->title, $item->author, $item->category, $item->status]],
        ];
        abort_unless(isset($definitions[$resource]), 404);
        [$model, $label, $headers, $map] = $definitions[$resource];
        $query = $model::query()->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))->when($request->status, fn ($q, $status) => $q->where('status', $status));
        if ($request->category && in_array($resource, ['initiatives', 'stories'], true)) $query->where('category', $request->category);
        if ($request->from && $resource === 'events') $query->whereDate('event_date', '>=', $request->from);
        if ($request->to && $resource === 'events') $query->whereDate('event_date', '<=', $request->to);
        if ($request->from && $resource === 'impact') $query->whereDate('activity_date', '>=', $request->from);
        if ($request->to && $resource === 'impact') $query->whereDate('activity_date', '<=', $request->to);

        return $this->csv(strtolower($label) . '.csv', $headers, $query->latest()->cursor(), $map);
    }

    private function csv(string $filename, array $headers, iterable $rows, callable $map): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows, $map): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $map($row));
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}