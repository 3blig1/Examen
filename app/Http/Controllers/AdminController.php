<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExamRegistration;
use App\Models\ExamResult;
use App\Models\Exam;
use App\Models\ExamExaminerAssignment;
use App\Models\PaymentHistory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\PaymentConfirmed;
use App\Mail\RegistrationCancelled;
use App\Mail\ResultsPublished;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ExamRegistrationsExport;
use App\Exports\ExamResultsExport;

class AdminController extends Controller
{
    /**
     * List all students
     */
    public function students()
    {
        $name = request('name');
        $email = request('email');
        $country = request('country');

        $students = User::where('role', 'student')
            ->when($name, function ($query) use ($name) {
                $query->where(function ($sub) use ($name) {
                    $sub->where('first_name', 'like', "%{$name}%")
                        ->orWhere('last_name', 'like', "%{$name}%");
                });
            })
            ->when($email, function ($query) use ($email) {
                $query->where('email', 'like', "%{$email}%");
            })
            ->when($country, function ($query) use ($country) {
                $query->where('country', 'like', "%{$country}%");
            })
            ->paginate(15)
            ->withQueryString();
        return view('admin.students', ['students' => $students]);
    }

    /**
     * View student details
     */
    public function studentDetails(User $user)
    {
        if ($user->role !== 'student') {
            abort(404);
        }

        $registrations = $user->examRegistrations()->with('exam')->get();
        $results = $user->examResults()->with('exam')->get();
        $payments = $user->paymentHistories()->with('registration.exam')->orderBy('paid_at', 'desc')->get();

        return view('admin.student-details', [
            'student' => $user,
            'registrations' => $registrations,
            'results' => $results,
            'payments' => $payments,
        ]);
    }

    /**
     * Manage exams
     */
    public function exams()
    {
        Exam::refreshStatuses();

        $q = request('q');
        $level = request('level');
        $location = request('location');
        $status = request('status');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $exams = Exam::orderBy('date', 'desc')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%")
                        ->orWhere('status', 'like', "%{$q}%");
                });
            })
            ->when($level, function ($query) use ($level) {
                $query->where('name', $level);
            })
            ->when($location, function ($query) use ($location) {
                $query->where('location', 'like', "%{$location}%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('date', '<=', $dateTo);
            })
            ->paginate(15)
            ->withQueryString();
        return view('admin.exams', ['exams' => $exams]);
    }

    /**
     * Create exam form
     */
    public function createExam()
    {
        return view('admin.create-exam');
    }

    /**
     * Store new exam
     */
    public function storeExam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date|after:today',
            'registration_deadline' => 'nullable|date|before_or_equal:date',
            'exam_type' => 'required|in:complete,module',
            'available_modules' => 'nullable|array',
            'available_modules.*' => 'in:reading,writing,listening,speaking',
            'max_participants' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'price_per_module' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        // Convert available_modules to array if it's null
        $validated['available_modules'] = $validated['available_modules'] ?? [];

        $exam = Exam::create($validated);

        $this->logActivity('exam_created', Exam::class, $exam->id, "Exam created: {$exam->name}");

        return redirect()->route('admin.exams')
            ->with('success', 'Exam created successfully');
    }

    /**
     * Edit exam form
     */
    public function editExam(Exam $exam)
    {
        return view('admin.edit-exam', ['exam' => $exam]);
    }

    /**
     * Update exam
     */
    public function updateExam(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'registration_deadline' => 'nullable|date|before_or_equal:date',
            'exam_type' => 'required|in:complete,module',
            'available_modules' => 'nullable|array',
            'available_modules.*' => 'in:reading,writing,listening,speaking',
            'max_participants' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'price_per_module' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:upcoming,ongoing,completed',
        ]);

        // Convert available_modules to array if it's null
        $validated['available_modules'] = $validated['available_modules'] ?? [];

        $exam->update($validated);

        $this->logActivity('exam_updated', Exam::class, $exam->id, "Exam updated: {$exam->name}");

        return redirect()->route('admin.exams')
            ->with('success', 'Exam updated successfully');
    }

    /**
     * Delete exam
     */
    public function deleteExam(Exam $exam)
    {
        $this->logActivity('exam_deleted', Exam::class, $exam->id, "Exam deleted: {$exam->name}");
        $exam->delete();
        return back()->with('success', 'Exam deleted successfully');
    }

    /**
     * View exam registrations
     */
    public function examRegistrations(Exam $exam)
    {
        $registrations = ExamRegistration::where('exam_id', $exam->id)
            ->with('user')
            ->paginate(15);

        return view('admin.exam-registrations', [
            'exam' => $exam,
            'registrations' => $registrations,
        ]);
    }

    /**
     * Manage examiner assignments for an exam
     */
    public function manageExaminerAssignments(Exam $exam)
    {
        $examiners = User::where('role', 'examiner')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $assignments = ExamExaminerAssignment::where('exam_id', $exam->id)
            ->get()
            ->keyBy('examiner_id');

        return view('admin.exams-examiners', [
            'exam' => $exam,
            'examiners' => $examiners,
            'assignments' => $assignments,
        ]);
    }

    /**
     * Store examiner assignments for an exam
     */
    public function storeExaminerAssignments(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'assignments' => 'nullable|array',
            'assignments.*' => 'nullable|array',
            'assignments.*.*' => 'in:reading,writing,listening,speaking',
        ]);

        $validExaminerIds = User::where('role', 'examiner')->pluck('id')->all();
        $assignments = $validated['assignments'] ?? [];

        ExamExaminerAssignment::where('exam_id', $exam->id)->delete();

        foreach ($assignments as $examinerId => $modules) {
            if (!in_array((int) $examinerId, $validExaminerIds, true)) {
                continue;
            }

            $modules = array_values(array_unique($modules));
            if (count($modules) === 0) {
                continue;
            }

            ExamExaminerAssignment::create([
                'exam_id' => $exam->id,
                'examiner_id' => (int) $examinerId,
                'modules' => $modules,
            ]);
        }

        return back()->with('success', 'Affectations des examinateurs mises a jour');
    }

    /**
     * View ongoing exam tasks by examiner
     */
    public function examinerTasks()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        Exam::refreshStatuses();

        $statusFilter = request('status', 'all');
        if (!in_array($statusFilter, ['all', 'ongoing', 'upcoming'], true)) {
            $statusFilter = 'all';
        }

        $examinerId = request('examiner');
        if ($examinerId !== null && !is_numeric($examinerId)) {
            $examinerId = null;
        }

        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $filteredStatuses = $statusFilter === 'all'
            ? ['ongoing', 'upcoming']
            : [$statusFilter];

        $examiners = User::where('role', 'examiner')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $examFilter = function ($query) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $query->whereDate('date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('date', '<=', $dateTo);
            }
        };

        $assignmentsByExaminer = ExamExaminerAssignment::with(['exam', 'examiner'])
            ->when($examinerId, function ($query) use ($examinerId) {
                $query->where('examiner_id', (int) $examinerId);
            })
            ->whereHas('exam', function ($query) use ($filteredStatuses, $examFilter) {
                $query->whereIn('status', $filteredStatuses);
                $examFilter($query);
            })
            ->orderBy('examiner_id')
            ->orderBy('exam_id')
            ->get()
            ->groupBy('examiner_id');

        $ongoingCount = ExamExaminerAssignment::when($examinerId, function ($query) use ($examinerId) {
                $query->where('examiner_id', (int) $examinerId);
            })
            ->whereHas('exam', function ($query) use ($examFilter) {
                $query->where('status', 'ongoing');
                $examFilter($query);
            })
            ->count();

        $upcomingCount = ExamExaminerAssignment::when($examinerId, function ($query) use ($examinerId) {
                $query->where('examiner_id', (int) $examinerId);
            })
            ->whereHas('exam', function ($query) use ($examFilter) {
                $query->where('status', 'upcoming');
                $examFilter($query);
            })
            ->count();

        return view('admin.examiner-tasks', [
            'assignmentsByExaminer' => $assignmentsByExaminer,
            'statusFilter' => $statusFilter,
            'ongoingCount' => $ongoingCount,
            'upcomingCount' => $upcomingCount,
            'examiners' => $examiners,
            'examinerId' => $examinerId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Confirm registration
     */
    public function confirmRegistration(ExamRegistration $registration)
    {
        $registration->confirm();

        PaymentHistory::create([
            'exam_registration_id' => $registration->id,
            'user_id' => $registration->user_id,
            'amount' => $registration->amount_paid ?? 0,
            'method' => 'manual',
            'status' => 'confirmed',
            'paid_at' => now(),
        ]);

        $this->logActivity('payment_confirmed', ExamRegistration::class, $registration->id, "Payment confirmed for registration #{$registration->id}");

        Mail::to($registration->user->email)->send(new PaymentConfirmed($registration));

        return back()->with('success', 'Registration confirmed');
    }

    /**
     * Reject registration
     */
    public function rejectRegistration(ExamRegistration $registration)
    {
        $registration->update([
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ]);
        $registration->exam->decrement('registered_count');

        $this->logActivity('registration_cancelled', ExamRegistration::class, $registration->id, "Registration cancelled #{$registration->id}");

        Mail::to($registration->user->email)->send(new RegistrationCancelled($registration));

        return back()->with('success', 'Registration rejected');
    }

    /**
     * Enter exam results
     */
    public function enterResults(Exam $exam)
    {
        $registrations = ExamRegistration::where('exam_id', $exam->id)
            ->where('status', '!=', 'cancelled')
            ->with('user', 'result')
            ->get();

        return view('admin.enter-results', [
            'exam' => $exam,
            'registrations' => $registrations,
        ]);
    }

    /**
     * View results for a completed exam
     */
    public function examResults(Exam $exam)
    {
        if ($exam->status !== 'completed') {
            return back()->with('error', __('messages.exam_not_completed'));
        }

        $registrations = ExamRegistration::where('exam_id', $exam->id)
            ->whereHas('result', function ($query) {
                $query->whereNotNull('published_at');
            })
            ->with('user', 'result')
            ->get();

        return view('admin.exam-results', [
            'exam' => $exam,
            'registrations' => $registrations,
        ]);
    }

    /**
     * List completed exams with results link
     */
    public function completedExamResults()
    {
        $q = request('q');
        $level = request('level');
        $location = request('location');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $exams = Exam::where('status', 'completed')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                });
            })
            ->when($level, function ($query) use ($level) {
                $query->where('name', $level);
            })
            ->when($location, function ($query) use ($location) {
                $query->where('location', 'like', "%{$location}%");
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('date', '<=', $dateTo);
            })
            ->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.completed-exam-results', [
            'exams' => $exams,
        ]);
    }

    /**
     * Export registrations for an exam (PDF)
     */
    public function exportRegistrationsPdf(Exam $exam)
    {
        $registrations = ExamRegistration::where('exam_id', $exam->id)
            ->with('user')
            ->get();

        $examinerAssignments = $this->getExaminerAssignmentsByType($exam->id);

        $pdf = Pdf::loadView('admin.exports.registrations-pdf', [
            'exam' => $exam,
            'registrations' => $registrations,
            'examinerAssignments' => $examinerAssignments,
        ]);

        return $pdf->download("registrations-exam-{$exam->id}.pdf");
    }

    private function getExaminerAssignmentsByType(int $examId): array
    {
        $assignments = ExamExaminerAssignment::where('exam_id', $examId)
            ->with('examiner')
            ->get();

        $written = [];
        $oral = [];

        foreach ($assignments as $assignment) {
            $modules = $assignment->modules ?? [];
            $examiner = $assignment->examiner;
            $name = $examiner
                ? trim($examiner->first_name . ' ' . $examiner->last_name)
                : '';

            if ($name === '') {
                continue;
            }

            if (array_intersect($modules, ['reading', 'listening', 'writing'])) {
                $written[] = $name;
            }

            if (in_array('speaking', $modules, true)) {
                $oral[] = $name;
            }
        }

        sort($written);
        sort($oral);

        return [
            'written' => array_values(array_unique($written)),
            'oral' => array_values(array_unique($oral)),
        ];
    }

    /**
     * Export registrations for an exam (Excel)
     */
    public function exportRegistrationsExcel(Exam $exam)
    {
        return Excel::download(new ExamRegistrationsExport($exam->id), "registrations-exam-{$exam->id}.xlsx");
    }

    /**
     * Export results for an exam (PDF)
     */
    public function exportResultsPdf(Exam $exam)
    {
        $registrations = ExamRegistration::where('exam_id', $exam->id)
            ->whereHas('result', function ($query) {
                $query->whereNotNull('published_at');
            })
            ->with('user', 'result')
            ->get();

        $pdf = Pdf::loadView('admin.exports.results-pdf', [
            'exam' => $exam,
            'registrations' => $registrations,
        ]);

        return $pdf->download("results-exam-{$exam->id}.pdf");
    }

    /**
     * Export results for an exam (Excel)
     */
    public function exportResultsExcel(Exam $exam)
    {
        return Excel::download(new ExamResultsExport($exam->id), "results-exam-{$exam->id}.xlsx");
    }

    /**
     * Payment history
     */
    public function paymentHistory()
    {
        $userId = request('user_id');
        $examId = request('exam_id');

        $payments = PaymentHistory::with(['user', 'registration.exam'])
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when($examId, function ($query) use ($examId) {
                $query->whereHas('registration', function ($sub) use ($examId) {
                    $sub->where('exam_id', $examId);
                });
            })
            ->orderBy('paid_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $users = User::orderBy('first_name')->orderBy('last_name')->get();
        $exams = Exam::orderBy('date', 'desc')->get();

        $totalsByExam = PaymentHistory::query()
            ->join('exam_registrations', 'payment_histories.exam_registration_id', '=', 'exam_registrations.id')
            ->join('exams', 'exam_registrations.exam_id', '=', 'exams.id')
            ->when($userId, function ($query) use ($userId) {
                $query->where('payment_histories.user_id', $userId);
            })
            ->when($examId, function ($query) use ($examId) {
                $query->where('exam_registrations.exam_id', $examId);
            })
            ->select('exams.id', 'exams.name', 'exams.date', DB::raw('SUM(payment_histories.amount) as total_amount'))
            ->groupBy('exams.id', 'exams.name', 'exams.date')
            ->orderBy('exams.date', 'desc')
            ->get();

        return view('admin.payment-history', [
            'payments' => $payments,
            'users' => $users,
            'exams' => $exams,
            'totalsByExam' => $totalsByExam,
        ]);
    }

    /**
     * Activity logs
     */
    public function activityLogs()
    {
        $userId = request('user_id');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $superAdminEmail = config('app.super_admin_email');

        $logs = ActivityLog::with('user')
            ->when($superAdminEmail, function ($query) use ($superAdminEmail) {
                $query->whereHas('user', function ($sub) use ($superAdminEmail) {
                    $sub->where('email', '!=', $superAdminEmail);
                });
            })
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('first_name')->orderBy('last_name')->get();

        return view('admin.activity-logs', [
            'logs' => $logs,
            'users' => $users,
        ]);
    }

    /**
     * Store exam result
     */
    public function storeResult(Request $request, ExamRegistration $registration)
    {
        $request->validate([
            'level_achieved' => 'nullable|string|in:A1,A2,B1,B2,C1,C2',
            'status' => 'required|in:passed,failed',
            'remarks' => 'nullable|string',
        ]);

        $cleanScore = function ($value) {
            if (is_null($value) || $value === '') {
                return 0;
            }
            return (int) $value;
        };

        $readingScore = $cleanScore($request->input('reading_score'));
        $listeningScore = $cleanScore($request->input('listening_score'));
        $writingScore = $cleanScore($request->input('writing_score'));
        $speakingScore = $cleanScore($request->input('speaking_score'));

        foreach ([
            'reading_score' => $readingScore,
            'listening_score' => $listeningScore,
            'writing_score' => $writingScore,
            'speaking_score' => $speakingScore,
        ] as $field => $score) {
            if ($score < 0 || $score > 100) {
                return back()->withErrors([$field => 'Le score doit être entre 0 et 100']);
            }
        }

        $selectedModules = $registration->selected_modules ?? [];
        $scores = [];

        if (in_array('reading', $selectedModules)) {
            $scores[] = $readingScore;
        }
        if (in_array('listening', $selectedModules)) {
            $scores[] = $listeningScore;
        }
        if (in_array('writing', $selectedModules)) {
            $scores[] = $writingScore;
        }
        if (in_array('speaking', $selectedModules)) {
            $scores[] = $speakingScore;
        }

        $totalScore = !empty($scores) ? array_sum($scores) / count($scores) : 0;
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
                'exam_id' => $registration->exam_id,
                'user_id' => $registration->user_id,
                'reading_score' => $readingScore,
                'listening_score' => $listeningScore,
                'writing_score' => $writingScore,
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
     * Publish results
     */
    public function publishResults(Exam $exam)
    {
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

        return back()->with('success', "Results published successfully ($updated)");
    }

    /**
     * Show user management page (assign roles)
     */
    public function manageUsers()
    {
        $name = request('name');
        $email = request('email');
        $role = request('role');

        $superAdminEmail = config('app.super_admin_email');

        $users = User::orderBy('created_at', 'desc')
            ->when($superAdminEmail, function ($query) use ($superAdminEmail) {
                $query->where('email', '!=', $superAdminEmail);
            })
            ->when($name, function ($query) use ($name) {
                $query->where(function ($sub) use ($name) {
                    $sub->where('first_name', 'like', "%{$name}%")
                        ->orWhere('last_name', 'like', "%{$name}%");
                });
            })
            ->when($email, function ($query) use ($email) {
                $query->where('email', 'like', "%{$email}%");
            })
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->paginate(20)
            ->withQueryString();
        return view('admin.users', ['users' => $users]);
    }

    /**
     * Show create user page for admin/examiner
     */
    public function createUser()
    {
        return view('admin.users-create');
    }

    /**
     * Create an admin or examiner user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|in:mr,mme,mlle,dr',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,examiner',
        ]);

        User::create([
            'title' => $validated['title'],
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', "Utilisateur {$validated['role']} cree avec succes");
    }

    /**
     * Update a user's role
     */
    public function updateUserRole(\Illuminate\Http\Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'You cannot change the super admin role.');
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,examiner,student',
        ]);

        // Prevent demoting self
        if (auth()->id() === $user->id && $validated['role'] !== 'admin') {
            return back()->with('error', 'You cannot change your own admin role.');
        }

        $user->role = $validated['role'];
        $user->save();

        return back()->with('success', 'User role updated successfully');
    }

    /**
     * Reset super admin password to the value in .env
     */
    public function resetSuperAdminPassword()
    {
        $superAdminEmail = config('app.super_admin_email');
        $superAdminPassword = config('app.super_admin_password');

        if (empty($superAdminEmail) || empty($superAdminPassword)) {
            return back()->with('error', 'Super admin email or password is not configured.');
        }

        $user = User::where('email', $superAdminEmail)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Super Admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => $superAdminEmail,
                'password' => Hash::make($superAdminPassword),
                'role' => 'admin',
                'is_active' => true,
            ]);
        } else {
            $user->forceFill([
                'password' => Hash::make($superAdminPassword),
                'role' => 'admin',
            ])->save();
        }

        return back()->with('success', 'Super admin password reset successfully.');
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
