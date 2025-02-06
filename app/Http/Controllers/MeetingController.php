<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Meetings\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        return view('meetings.all-meetings');
    }

    public function create()
    {
        return view('meetings.create-new-meeting');
    }
}