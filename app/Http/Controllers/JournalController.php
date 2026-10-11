<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where(function ($query) use ($q) {
                $query->where('description', 'like', "%{$q}%")
                    ->orWhere('event', 'like', "%{$q}%")
                    ->orWhere('log_name', 'like', "%{$q}%");
            });
        }

        $activities = $query->paginate(30)->withQueryString();

        return view('journal.index', compact('activities'));
    }

    public function clear()
    {
        Activity::query()->delete();

        return back()->with('success', 'Journal d activites vide.');
    }
}
