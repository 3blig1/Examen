<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamRegistration;
use App\Models\ExamExaminerAssignment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResultsPublished;
use App\Support\FlashMessage;

class ExaminerController extends Controller
{
    /**
     * Show examiner dashboard
     */
    public function dashboard()
    {
        if (!auth()->user()->isExaminer()) {
            abort(403);
        }

        Exam::refreshStatuses();

        $upcomingExams = Exam::where('status', 'upcoming')
            ->orderBy('date')
            ->take(6)
            ->get();

        $ongoingExams = Exam::where('status', 'ongoing')
            ->orderBy('date')
            ->take(6)
            ->get();

        $completedExams = Exam::where('status', 'completed')
            ->orderBy('date', 'desc')
            ->take(6)
            ->get();

        $pendingResults = ExamRegistration::whereHas('exam', function ($query) {
                $query->where('status', 'completed');
            })
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) {
                $query->whereDoesntHave('result')
                      ->orWhereHas('result', function ($subQuery) {
                          $subQuery->where('status', 'pending');
                      });
            })
            ->count();

        $pendingAssignmentsCount = ExamExaminerAssignment::where('examiner_id', auth()->id())
            ->whereHas('exam', function ($query) {
                $query->where('status', '!=', 'completed');
            })
            ->count();

        $pendingAssignments = ExamExaminerAssignment::where('examiner_id', auth()->id())
            ->whereHas('exam', function ($query) {
                $query->where('status', '!=', 'completed');
            })
            ->with('exam')
            ->orderBy('exam_id')
            ->take(6)
            ->get();

        $completedAssignmentsCount = ExamExaminerAssignment::where('examiner_id', auth()->id())
            ->whereHas('exam', function ($query) {
                $query->where('status', 'completed');
            })
            ->count();

        $completedAssignments = ExamExaminerAssignment::where('examiner_id', auth()->id())
            ->whereHas('exam', function ($query) {
                $query->where('status', 'completed');
            })
            ->with('exam')
            ->orderBy('exam_id')
            ->take(6)
            ->get();

        return view('dashboard.examiner', [
            'upcomingExams' => $upcomingExams,
            'ongoingExams' => $ongoingExams,
            'completedExams' => $completedExams,
            'pendingAssignments' => $pendingAssignments,
            'completedAssignments' => $completedAssignments,
            'pendingAssignmentsCount' => $pendingAssignmentsCount,
            'completedAssignmentsCount' => $completedAssignmentsCount,
            'statistics' => [
                'pendingResults' => $pendingResults,
            ],
        ]);
    }

    /**
     * Show exam registrations for entering results
     */
    public function enterResults(Exam $exam)
    {
        if (!auth()->user()->isExaminer()) {
            abort(403);
        }

        $registrations = ExamRegistration::where('exam_id', $exam->id)
            ->where('status', '!=', 'cancelled')
            ->with('user', 'result')
            ->get();

        return view('examiner.enter-results', [
            'exam' => $exam,
            'registrations' => $registrations,
        ]);
    }

    /**
     * Store or update exam result
     */
    public function storeResult(Request $request, ExamRegistration $registration)
    {
        if (!auth()->user()->isExaminer()) {
            abort(403);
        }

        $allowedModules = ['reading', 'listening', 'writing', 'speaking'];
        $selectedModules = array_values(array_intersect(
            $registration->selected_modules ?? $allowedModules,
            $allowedModules
        ));
        if ($selectedModules === []) {
            $selectedModules = $allowedModules;
        }

        $rules = [
            'level_achieved' => 'nullable|string|in:A1,A2,B1,B2,C1,C2',
            'status' => 'required|string|in:passed,failed',
            'remarks' => 'nullable|string',
        ];
        foreach ($selectedModules as $module) {
            $rules[$module . '_score'] = 'required|integer|min:0|max:100';
        }

        $validated = $request->validate($rules);
        $moduleScores = array_fill_keys($allowedModules, null);
        $scores = [];

        foreach ($selectedModules as $module) {
            $moduleScores[$module] = (int) $validated[$module . '_score'];
            $scores[] = $moduleScores[$module];
        }

        $totalScore = !empty($scores) ? array_sum($scores) / count($scores) : 0;

        // Create or update result
        ExamResult::updateOrCreate(
            ['exam_registration_id' => $registration->id],
            [
                'user_id' => $registration->user_id,
                'exam_id' => $registration->exam_id,
                'reading_score' => $moduleScores['reading'],
                'writing_score' => $moduleScores['writing'],
                'listening_score' => $moduleScores['listening'],
                'speaking_score' => $moduleScores['speaking'],
                'total_score' => $totalScore,
                'level_achieved' => $validated['level_achieved'] ?? null,
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
            ]
        );

        return back()->with('success', FlashMessage::translated('flash.result_saved'));
    }

    /**
     * Publish exam results
     */
    public function publishResults(Request $request, Exam $exam)
    {
        if (!auth()->user()->isExaminer()) {
            abort(403);
        }

        $results = ExamResult::where('exam_id', $exam->id)
            ->whereNull('published_at')
            ->get();

        $updated = ExamResult::where('exam_id', $exam->id)
            ->whereNull('published_at')
            ->update(['published_at' => now()]);

        foreach ($results as $result) {
            Mail::to($result->user->email)->send(new ResultsPublished($exam, $result->user));
        }

        $this->logActivity('results_published', Exam::class, $exam->id, "Results published for exam {$exam->name}");

        return back()->with('success', FlashMessage::translated('flash.examiner_results_published_count', ['count' => $updated]));
    }

    private function logActivity(string $action, ?string $subjectType = null, ?int $subjectId = null, ?string $description = null): void
    {
        if (auth()->user()?->isSuperAdmin()) {
            return;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
        ]);
    }
}
