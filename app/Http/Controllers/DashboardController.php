<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamRegistration;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show student dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $bankInfo = config('app.bank') ?? [];

        // Get user's registrations
        $registrations = ExamRegistration::where('user_id', $user->id)
            ->with(['exam', 'result'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's results (students see only published)
        if ($user->isAdmin() || $user->isExaminer()) {
            $results = ExamResult::where('user_id', $user->id)
                ->with(['exam', 'registration'])
                ->orderBy('published_at', 'desc')
                ->get();
        } else {
            $registrationIds = $registrations
                ->where('payment_status', 'paid')
                ->pluck('id');
            $results = ExamResult::whereIn('exam_registration_id', $registrationIds)
                ->whereNotNull('published_at')
                ->with(['exam', 'registration'])
                ->orderBy('published_at', 'desc')
                ->get();
        }

        // Get upcoming exams for registration
        $upcomingExams = Exam::where('status', 'upcoming')
            ->where('date', '>', now())
            ->where(function ($query) {
                $query->whereNull('registration_deadline')
                      ->orWhere('registration_deadline', '>=', now());
            })
            ->where('registered_count', '<', \DB::raw('max_participants'))
            ->orderBy('date')
            ->get();

        // Count statistics
        $totalRegistrations = $registrations->count();
        $confirmedRegistrations = $registrations->where('status', 'confirmed')->count();
        $passedExams = $results->where('status', 'passed')->count();
        $totalExamsTaken = $results->count();

        return view('dashboard.index', [
            'registrations' => $registrations,
            'results' => $results,
            'upcomingExams' => $upcomingExams,
            'bankInfo' => $bankInfo,
            'statistics' => [
                'totalRegistrations' => $totalRegistrations,
                'confirmedRegistrations' => $confirmedRegistrations,
                'passedExams' => $passedExams,
                'totalExamsTaken' => $totalExamsTaken,
            ],
        ]);
    }

    /**
     * Show a single exam result details (student or admin/examiner)
     */
    public function showResult(\App\Models\ExamResult $result)
    {
        $user = auth()->user();
        $result->loadMissing('registration');

        // Only allow owner, admin or examiner to view
        if (! $user->isAdmin() && ! $user->isExaminer() && $result->user_id !== $user->id) {
            abort(403);
        }

        // Students can only view results for registrations with confirmed payment.
        if (! $user->isAdmin() && ! $user->isExaminer() && $result->registration?->payment_status !== 'paid') {
            return redirect()->route('dashboard')
                ->with('error', __('messages.payment_required'));
        }

        // Students cannot view unpublished results
        if (!$user->isAdmin() && !$user->isExaminer() && $result->published_at === null) {
            return redirect()->route('dashboard')
                ->with('error', __('messages.results_not_published'));
        }

        $result->load(['exam', 'registration', 'user']);

        return view('results.show', [
            'result' => $result,
        ]);
    }

    /**
     * Show admin dashboard
     */
    public function admin()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $totalStudents = \App\Models\User::where('role', 'student')->count();
        $totalRegistrations = ExamRegistration::count();
        $confirmedRegistrations = ExamRegistration::where('status', 'confirmed')->count();
        $pendingRegistrations = ExamRegistration::where('status', 'pending')->count();
        $totalExams = Exam::count();
        $upcomingExams = Exam::where('status', 'upcoming')->count();

        $recentRegistrations = ExamRegistration::with(['user', 'exam'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.admin', [
            'statistics' => [
                'totalStudents' => $totalStudents,
                'totalRegistrations' => $totalRegistrations,
                'confirmedRegistrations' => $confirmedRegistrations,
                'pendingRegistrations' => $pendingRegistrations,
                'totalExams' => $totalExams,
                'upcomingExams' => $upcomingExams,
            ],
            'recentRegistrations' => $recentRegistrations,
        ]);
    }

    /**
     * Show examiner dashboard
     */
    public function examiner()
    {
        if (!auth()->user()->isExaminer()) {
            abort(403);
        }

        $upcomingExams = Exam::where('status', 'upcoming')
            ->orderBy('date')
            ->get();

        $completedExams = Exam::where('status', 'completed')
            ->orderBy('date', 'desc')
            ->get();

        $pendingResults = ExamResult::where('status', 'pending')
            ->with(['user', 'exam'])
            ->count();

        return view('dashboard.examiner', [
            'upcomingExams' => $upcomingExams,
            'completedExams' => $completedExams,
            'statistics' => [
                'pendingResults' => $pendingResults,
            ],
        ]);
    }
}
