<?php

namespace App\Http\Controllers;

use App\Models\Cause;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CauseController extends Controller
{
    public function index(Request $request)
    {
        $query = Cause::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $causes = $query->orderBy('display_order')->paginate(12);

        return inertia('Causes', compact('causes'));
    }

    public function show(Cause $cause)
    {
        $initiatives = $cause->initiatives()->published()->get();

        return inertia('CauseShow', compact('cause', 'initiatives'));
    }
}
