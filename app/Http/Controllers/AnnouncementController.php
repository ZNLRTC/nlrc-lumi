<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        return view('announcements.index');
    }

    public function show($id): View
    {
        $current_announcement = Announcement::find($id);
        $user = User::findOrFail(Auth::user()->id);

        if ($user->cannot('view', $current_announcement)) {
            abort(403);
        }

        return view('announcements.detail', ['current_announcement' => $current_announcement]);
    }
}
