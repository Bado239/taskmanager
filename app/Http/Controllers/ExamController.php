<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    
    public function index()
    {
        $projectsMaster = Project::where('type', 'master')
            ->whereRaw('is_active = TRUE')
            ->orderBy('title')
            ->get();

        $exams = Exam::with('project')
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->get();

        return view('exams.index', compact(
            'projectsMaster',
            'exams'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([

            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id')
                    ->where('type', 'master')
                    ->where('is_active', true),
            ],
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1|max:1440',
            'target_score' => 'nullable|numeric|min:0|max:20',
            'notes' => 'nullable|string|max:3000',
        ]);

        Exam::create($data);

        return redirect()
            ->route('exams.index')
            ->with('success', 'Examen enregistré avec succès !');
    }

}
