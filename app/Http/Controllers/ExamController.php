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
            // Examens passés après les examens à venir
            ->orderByRaw(
                'CASE WHEN exam_date < CURRENT_DATE THEN 1 ELSE 0 END ASC'
            )
            // Examens à venir : les plus proches en premier
            ->orderByRaw(
                'CASE WHEN exam_date >= CURRENT_DATE THEN exam_date END ASC'
            )
            // Examens passés : les plus récents en premier
            ->orderByRaw(
                'CASE WHEN exam_date < CURRENT_DATE THEN exam_date END DESC'
            )
            ->orderBy('start_time')
            ->orderBy('id')
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

    
    public function update(Request $request, Exam $exam)
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

        $exam->update($data);

        return redirect()
            ->route('exams.index')
            ->with('success', 'Examen modifié avec succès !');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()
            ->route('exams.index')
            ->with('success', 'Examen supprimé définitivement !');
    }


}
