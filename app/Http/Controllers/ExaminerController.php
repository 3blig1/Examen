<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamRegistration;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResultsPublished;

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
            ->get();

        $ongoingExams = Exam::where('status', 'ongoing')
            ->orderBy('date')
            ->get();

        $completedExams = Exam::where('status', 'completed')
            ->orderBy('date', 'desc')
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

        return view('dashboard.examiner', [
            'upcomingExams' => $upcomingExams,
            'ongoingExams' => $ongoingExams,
            'completedExams' => $completedExams,
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

        // Validate optional fields
        $request->validate([
            'level_achieved' => 'nullable|string|in:A1,A2,B1,B2,C1,C2',
            'status' => 'required|string|in:passed,failed',
        ]);

        // Clean and normalize score values (convert empty/null to 0)
        $cleanScore = function($value) {
            if (is_null($value) || $value === '') {
                return 0;
            }
            return (int)$value;
        };

        $readingScore = $cleanScore($request->input('reading_score'));
        $writingScore = $cleanScore($request->input('writing_score'));
        $listeningScore = $cleanScore($request->input('listening_score'));
        $speakingScore = $cleanScore($request->input('speaking_score'));

        // Validate score ranges
        foreach ([
            'reading_score' => $readingScore,
            'writing_score' => $writingScore,
            'listening_score' => $listeningScore,
            'speaking_score' => $speakingScore
        ] as $field => $score) {
            if ($score < 0 || $score > 100) {
                return back()->withErrors([$field => "Le score doit être entre 0 et 100"]);
            }
        }

        // Calculate total score based on selected modules
        $selectedModules = $registration->selected_modules ?? [];
        $scores = [];
        
        if (in_array('reading', $selectedModules)) {
            $scores[] = $readingScore;
        }
        if (in_array('writing', $selectedModules)) {
            $scores[] = $writingScore;
        }
        if (in_array('listening', $selectedModules)) {
            $scores[] = $listeningScore;
        }
        if (in_array('speaking', $selectedModules)) {
            $scores[] = $speakingScore;
        }

        $totalScore = !empty($scores) ? array_sum($scores) / count($scores) : 0;

        // Create or update result
        $levelAchieved = $request->input('level_achieved');
        if ($levelAchieved === '') {
            $levelAchieved = null;
        }

        $status = $request->input('status');
        if ($status === '') {
            $status = null;
        }

        ExamResult::updateOrCreate(
            ['exam_registration_id' => $registration->id],
            [
                'user_id' => $registration->user_id,
                'exam_id' => $registration->exam_id,
                'reading_score' => $readingScore,
                'writing_score' => $writingScore,
                'listening_score' => $listeningScore,
                'speaking_score' => $speakingScore,
                'total_score' => $totalScore,
                'level_achieved' => $levelAchieved,
                'status' => $status,
                'remarks' => $request->input('remarks'),
            ]
        );

        return back()->with('success', 'Result saved successfully');
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

        return back()->with('success', "Published $updated results successfully");
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
