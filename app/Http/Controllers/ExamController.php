<?php

namespace App\Http\Controllers;

use App\Models\Exams\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        return view('exams.index');
    }

    public function show($type, $id)
    {
        $exam = Exam::findOrFail($id);

        if (strtolower($type) !== strtolower($exam->type)) {
            return redirect()->route('exams.index');
        }

        return view('exams.show', compact('exam', 'type'));
    }

    public function results()
    {
        return view('exams.results');
    }
}
