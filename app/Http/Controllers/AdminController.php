<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExamRegistration;
use App\Models\ExamResult;
use App\Models\Exam;
use App\Models\ExamExaminerAssignment;
use App\Models\EmailVerification;
use App\Models\PaymentHistory;
use App\Models\ActivityLog;
use App\Support\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\EmailVerificationMail;
use App\Mail\PaymentConfirmed;
use App\Mail\RegistrationCancelled;
use App\Mail\ResultsPublished;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ExamRegistrationsExport;
use App\Exports\ExamResultsExport;
use App\Exports\ExamResultsTemplateExport;
use App\Imports\ExamResultsRowsImport;

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
            ->with('success', FlashMessage::translated('messages.exam_created'));
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
            ->with('success', FlashMessage::translated('messages.exam_updated'));
    }

    /**
     * Delete exam
     */
    public function deleteExam(Exam $exam)
    {
        $this->logActivity('exam_deleted', Exam::class, $exam->id, "Exam deleted: {$exam->name}");
        $exam->delete();
        return back()->with('success', FlashMessage::translated('messages.exam_deleted'));
    }

    /**
     * View exam registrations
     */
    public function examRegistrations(Exam $exam)
    {
        $filter = request()->query('filter');
        if (!in_array($filter, ['paid', 'unpaid', 'cancelled', 'osd'], true)) {
            $filter = null;
        }

        $query = ExamRegistration::where('exam_id', $exam->id);
        $paymentCounts = [
            'paid' => (clone $query)->where('payment_status', 'paid')->count(),
            'unpaid' => (clone $query)->where('status', 'pending')->where('payment_status', 'unpaid')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'osd' => (clone $query)->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('status', 'confirmed')
                            ->where('payment_status', 'unpaid');
                    });
            })->count(),
        ];

        $registrations = (clone $query)
            ->when($filter === 'cancelled', function ($query) {
                $query->where('status', 'cancelled');
            })
            ->when($filter === 'paid', function ($query) {
                $query->where('payment_status', 'paid');
            })
            ->when($filter === 'unpaid', function ($query) {
                $query->where('status', 'pending')->where('payment_status', 'unpaid');
            })
            ->when($filter === 'osd', function ($query) {
                $query->where(function ($query) {
                    $query->where('payment_status', 'paid')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('status', 'confirmed')
                                ->where('payment_status', 'unpaid');
                        });
                });
            })
            ->with('user')
            ->paginate(15)
            ->withQueryString();

        return view('admin.exam-registrations', [
            'exam' => $exam,
            'registrations' => $registrations,
            'filter' => $filter,
            'paymentCounts' => $paymentCounts,
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

        return back()->with('success', FlashMessage::translated('flash.examiner_assignments_updated'));
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
        $confirmationResult = DB::transaction(function () use ($registration) {
            $lockedRegistration = ExamRegistration::whereKey($registration->id)->lockForUpdate()->firstOrFail();
            if ($lockedRegistration->payment_status === 'paid') {
                return 'already_paid';
            }
            if (in_array($lockedRegistration->status, ['cancelled', 'completed'], true)) {
                return 'inactive';
            }

            $lockedRegistration->confirm();

            PaymentHistory::create([
                'exam_registration_id' => $lockedRegistration->id,
                'user_id' => $lockedRegistration->user_id,
                'amount' => $lockedRegistration->amount_paid ?? 0,
                'method' => 'manual',
                'status' => 'confirmed',
                'paid_at' => now(),
            ]);

            return 'confirmed';
        });

        if ($confirmationResult !== 'confirmed') {
            return back()->with('error', FlashMessage::translated($confirmationResult === 'already_paid'
                ? 'messages.payment_already_confirmed'
                : 'messages.cannot_confirm_inactive_registration'));
        }

        $registration->refresh();

        $this->logActivity('payment_confirmed', ExamRegistration::class, $registration->id, "Payment confirmed for registration #{$registration->id}");

        Mail::to($registration->user->email)->send(new PaymentConfirmed($registration));

        return back()->with('success', FlashMessage::translated('messages.payment_confirmation_recorded'));
    }

    /**
     * Restore a cancelled registration when payment was actually received.
     */
    public function restorePaidRegistration(ExamRegistration $registration)
    {
        Exam::refreshStatuses();

        $restoreResult = DB::transaction(function () use ($registration) {
            $lockedRegistration = ExamRegistration::whereKey($registration->id)->lockForUpdate()->firstOrFail();
            $exam = Exam::whereKey($lockedRegistration->exam_id)->lockForUpdate()->firstOrFail();

            if ($lockedRegistration->status !== 'cancelled' || $lockedRegistration->payment_status === 'paid') {
                return 'invalid';
            }
            if ($exam->status === 'completed') {
                return 'completed';
            }
            if ($exam->registered_count >= $exam->max_participants) {
                return 'full';
            }

            $lockedRegistration->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_date' => now(),
                'confirmed_at' => now(),
            ]);
            $exam->increment('registered_count');

            $hasConfirmedPayment = PaymentHistory::where('exam_registration_id', $lockedRegistration->id)
                ->where('status', 'confirmed')
                ->exists();

            if (!$hasConfirmedPayment) {
                PaymentHistory::create([
                    'exam_registration_id' => $lockedRegistration->id,
                    'user_id' => $lockedRegistration->user_id,
                    'amount' => $lockedRegistration->amount_paid ?? 0,
                    'method' => 'manual',
                    'status' => 'confirmed',
                    'paid_at' => now(),
                    'notes' => 'Candidature rétablie après vérification du paiement reçu.',
                ]);
            }

            $this->logActivity(
                'cancelled_registration_restored_paid',
                ExamRegistration::class,
                $lockedRegistration->id,
                "Cancelled registration restored as paid #{$lockedRegistration->id}"
            );

            return 'restored';
        });

        if ($restoreResult !== 'restored') {
            $messageKey = match ($restoreResult) {
                'full' => 'messages.cannot_restore_exam_full',
                'completed' => 'messages.cannot_restore_completed_exam',
                default => 'messages.cannot_restore_registration',
            };

            return back()->with('error', FlashMessage::translated($messageKey));
        }

        $registration->refresh();
        Mail::to($registration->user->email)->send(new PaymentConfirmed($registration));

        return back()->with('success', FlashMessage::translated('messages.cancelled_registration_restored'));
    }

    /**
     * Restore a cancelled registration as confirmed without confirming payment.
     */
    public function restoreConfirmedUnpaidRegistration(ExamRegistration $registration)
    {
        Exam::refreshStatuses();

        $restoreResult = DB::transaction(function () use ($registration) {
            $lockedRegistration = ExamRegistration::whereKey($registration->id)->lockForUpdate()->firstOrFail();
            $exam = Exam::whereKey($lockedRegistration->exam_id)->lockForUpdate()->firstOrFail();

            if ($lockedRegistration->status !== 'cancelled' || $lockedRegistration->payment_status === 'paid') {
                return 'invalid';
            }
            if ($exam->status === 'completed') {
                return 'completed';
            }
            if ($exam->registered_count >= $exam->max_participants) {
                return 'full';
            }

            $lockedRegistration->update([
                'status' => 'confirmed',
                'payment_status' => 'unpaid',
                'payment_date' => null,
                'confirmed_at' => now(),
            ]);
            $exam->increment('registered_count');

            $this->logActivity(
                'cancelled_registration_restored_unpaid',
                ExamRegistration::class,
                $lockedRegistration->id,
                "Cancelled registration restored as confirmed, unpaid #{$lockedRegistration->id}"
            );

            return 'restored';
        });

        if ($restoreResult !== 'restored') {
            $messageKey = match ($restoreResult) {
                'full' => 'messages.cannot_restore_exam_full',
                'completed' => 'messages.cannot_restore_completed_exam',
                default => 'messages.cannot_restore_registration',
            };

            return back()->with('error', FlashMessage::translated($messageKey));
        }

        return back()->with('success', FlashMessage::translated('messages.cancelled_registration_restored_unpaid'));
    }

    /**
     * Undo an accidental payment confirmation while keeping an audit trail.
     */
    public function undoPaymentConfirmation(ExamRegistration $registration)
    {
        if ($registration->payment_status !== 'paid') {
            return back()->with('error', FlashMessage::translated('messages.payment_not_confirmed'));
        }

        if ($registration->status === 'completed') {
            return back()->with('error', FlashMessage::translated('messages.cannot_undo_completed_payment'));
        }

        DB::transaction(function () use ($registration) {
            $lockedRegistration = ExamRegistration::whereKey($registration->id)->lockForUpdate()->firstOrFail();
            if ($lockedRegistration->payment_status !== 'paid') {
                return;
            }

            $lockedRegistration->update([
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'payment_date' => null,
                'confirmed_at' => null,
            ]);

            $payment = PaymentHistory::where('exam_registration_id', $lockedRegistration->id)
                ->where('status', 'confirmed')
                ->latest('paid_at')
                ->first();

            if ($payment) {
                $payment->update([
                    'status' => 'reversed',
                    'notes' => trim(($payment->notes ? $payment->notes . "\n" : '') . 'Confirmation annulée par erreur administrative.'),
                ]);
            }

            $this->logActivity(
                'payment_confirmation_undone',
                ExamRegistration::class,
                $lockedRegistration->id,
                "Payment confirmation undone for registration #{$lockedRegistration->id}"
            );
        });

        return back()->with('success', FlashMessage::translated('messages.payment_confirmation_undone'));
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

        return back()->with('success', FlashMessage::translated('flash.registration_rejected'));
    }

    /**
     * Permanently delete an exam registration.
     */
    public function deleteRegistration(ExamRegistration $registration)
    {
        DB::transaction(function () use ($registration) {
            $exam = Exam::whereKey($registration->exam_id)->lockForUpdate()->firstOrFail();

            $this->logActivity(
                'registration_deleted',
                ExamRegistration::class,
                $registration->id,
                "Registration deleted #{$registration->id} for exam {$exam->name}"
            );

            if ($registration->status !== 'cancelled' && $exam->registered_count > 0) {
                $exam->decrement('registered_count');
            }

            $registration->delete();
        });

        return back()->with('success', FlashMessage::translated('flash.registration_deleted'));
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
     * Download a prefilled spreadsheet template for importing exam results.
     */
    public function exportResultsTemplate(Exam $exam)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        return Excel::download(
            new ExamResultsTemplateExport($exam->id),
            "exam-{$exam->id}-results-template.xlsx"
        );
    }

    /**
     * Import results from an Excel or CSV file, matched by registration ID and email.
     */
    public function importResults(Request $request, Exam $exam)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $request->validate([
            'results_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $uploadedFile = $request->file('results_file');
        $delimiter = ',';
        if (in_array(strtolower($uploadedFile->getClientOriginalExtension()), ['csv', 'txt'], true)) {
            $handle = fopen($uploadedFile->getRealPath(), 'r');
            $header = $handle ? fgets($handle) : false;
            if ($handle) {
                fclose($handle);
            }
            if (is_string($header) && substr_count($header, ';') > substr_count($header, ',')) {
                $delimiter = ';';
            }
        }

        $import = new ExamResultsRowsImport($delimiter);
        Excel::import($import, $uploadedFile);
        $rows = $import->rows ?? collect();

        if ($rows->count() > 500) {
            return back()->withErrors([
                'results_import' => __('messages.results_import_too_many_rows'),
            ]);
        }

        $errors = [];
        $updates = [];
        $seenRegistrationIds = [];
        $allowedModules = ['reading', 'listening', 'writing', 'speaking'];
        $examRegistrations = ExamRegistration::query()
            ->where('exam_id', $exam->id)
            ->where('status', '!=', 'cancelled')
            ->with(['user', 'result'])
            ->get();

        foreach ($rows as $index => $row) {
            $row = $row->toArray();
            $rowNumber = $index + 2; // The first row contains column headings.
            $hasResultData = collect([
                $row['reading_score'] ?? null,
                $row['listening_score'] ?? null,
                $row['writing_score'] ?? null,
                $row['speaking_score'] ?? null,
                $row['level_achieved'] ?? null,
                $row['status'] ?? null,
                $row['remarks'] ?? null,
            ])->contains(fn ($value) => $value !== null && trim((string) $value) !== '');

            // A prefilled template row with no scores entered is intentionally ignored.
            if (!$hasResultData) {
                continue;
            }

            $firstName = $this->normalizeCandidateName($row['first_name'] ?? null);
            $lastName = $this->normalizeCandidateName($row['last_name'] ?? null);
            $dateOfBirth = $this->normalizeCandidateBirthDate($row['date_of_birth'] ?? null);
            if ($firstName === '' || $lastName === '' || $dateOfBirth === null) {
                $errors[] = __('messages.results_import_missing_identity', ['row' => $rowNumber]);
                continue;
            }

            $matches = $examRegistrations->filter(function (ExamRegistration $candidateRegistration) use ($firstName, $lastName, $dateOfBirth) {
                $candidate = $candidateRegistration->user;

                return $candidate
                    && $this->normalizeCandidateName($candidate->first_name) === $firstName
                    && $this->normalizeCandidateName($candidate->last_name) === $lastName
                    && $candidate->date_of_birth?->format('Y-m-d') === $dateOfBirth;
            });

            if ($matches->count() !== 1) {
                $errors[] = $matches->isEmpty()
                    ? __('messages.results_import_candidate_not_found', ['row' => $rowNumber])
                    : __('messages.results_import_ambiguous_candidate', ['row' => $rowNumber]);
                continue;
            }

            $registration = $matches->first();
            if (isset($seenRegistrationIds[$registration->id])) {
                $errors[] = __('messages.results_import_duplicate_registration', ['row' => $rowNumber]);
                continue;
            }
            $seenRegistrationIds[$registration->id] = true;

            $selectedModules = array_values(array_intersect(
                $registration->selected_modules ?? $allowedModules,
                $allowedModules
            ));
            if ($selectedModules === []) {
                $selectedModules = $allowedModules;
            }

            $scores = array_fill_keys($allowedModules, null);
            foreach ($selectedModules as $module) {
                $value = $row[$module . '_score'] ?? null;
                if ($value === null || trim((string) $value) === '' || !is_numeric($value) || (float) $value < 0 || (float) $value > 100 || (float) $value !== floor((float) $value)) {
                    $errors[] = __('messages.results_import_invalid_score', [
                        'row' => $rowNumber,
                        'module' => __('messages.module_' . $module),
                    ]);
                    continue 2;
                }
                $scores[$module] = (int) $value;
            }

            $status = strtolower(trim((string) ($row['status'] ?? '')));
            if ($status !== '' && !in_array($status, ['passed', 'failed'], true)) {
                $errors[] = __('messages.results_import_invalid_status', ['row' => $rowNumber]);
                continue;
            }

            $level = strtoupper(trim((string) ($row['level_achieved'] ?? '')));
            if ($level !== '' && !in_array($level, ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'], true)) {
                $errors[] = __('messages.results_import_invalid_level', ['row' => $rowNumber]);
                continue;
            }

            $selectedScores = array_map(fn ($module) => $scores[$module], $selectedModules);
            $updates[] = [
                'registration' => $registration,
                'attributes' => [
                    'user_id' => $registration->user_id,
                    'exam_id' => $registration->exam_id,
                    'reading_score' => $scores['reading'],
                    'listening_score' => $scores['listening'],
                    'writing_score' => $scores['writing'],
                    'speaking_score' => $scores['speaking'],
                    'total_score' => array_sum($selectedScores) / count($selectedScores),
                    'level_achieved' => $level !== '' ? $level : null,
                    'status' => $status !== '' ? $status : ($registration->result?->status ?? 'pending'),
                    'remarks' => trim((string) ($row['remarks'] ?? '')) ?: null,
                ],
            ];
        }

        if ($errors !== []) {
            return back()->withErrors($errors);
        }

        if ($updates === []) {
            return back()->withErrors([
                'results_import' => __('messages.results_import_no_data'),
            ]);
        }

        DB::transaction(function () use ($updates) {
            foreach ($updates as $update) {
                ExamResult::updateOrCreate(
                    ['exam_registration_id' => $update['registration']->id],
                    $update['attributes']
                );
            }
        });

        $this->logActivity('results_imported', Exam::class, $exam->id, count($updates) . " exam results imported for {$exam->name}");

        return back()->with('success', FlashMessage::translated('messages.results_import_success', ['count' => count($updates)]));
    }

    private function normalizeCandidateName(mixed $value): string
    {
        $name = preg_replace('/\s+/u', ' ', trim((string) $value)) ?? '';

        return function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
    }

    private function normalizeCandidateBirthDate(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        // Excel may store a date cell as its numeric serial value.
        if (is_numeric($value) && (float) $value >= 1 && (float) $value <= 2958465) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            $date = \DateTimeImmutable::createFromFormat('!' . $format, $value);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * View results for a completed exam
     */
    public function examResults(Exam $exam)
    {
        if ($exam->status !== 'completed') {
            return back()->with('error', FlashMessage::translated('messages.exam_not_completed'));
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
        $filter = request()->query('filter');
        if (!in_array($filter, ['paid', 'unpaid', 'cancelled', 'osd'], true)) {
            $filter = null;
        }

        $registrations = ExamRegistration::where('exam_id', $exam->id)
            ->when($filter === 'cancelled', function ($query) {
                $query->where('status', 'cancelled');
            })
            ->when($filter === 'paid', function ($query) {
                $query->where('payment_status', 'paid');
            })
            ->when($filter === 'unpaid', function ($query) {
                $query->where('status', 'pending')->where('payment_status', 'unpaid');
            })
            ->when($filter === 'osd', function ($query) {
                $query->where(function ($query) {
                    $query->where('payment_status', 'paid')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('status', 'confirmed')
                                ->where('payment_status', 'unpaid');
                        });
                });
            })
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
        $filter = request()->query('filter');
        if (!in_array($filter, ['paid', 'unpaid', 'cancelled', 'osd'], true)) {
            $filter = null;
        }

        return Excel::download(
            new ExamRegistrationsExport($exam->id, $filter),
            "registrations-exam-{$exam->id}.xlsx"
        );
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
            'status' => 'required|in:passed,failed',
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

        ExamResult::updateOrCreate(
            ['exam_registration_id' => $registration->id],
            [
                'exam_id' => $registration->exam_id,
                'user_id' => $registration->user_id,
                'reading_score' => $moduleScores['reading'],
                'listening_score' => $moduleScores['listening'],
                'writing_score' => $moduleScores['writing'],
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

        return back()->with('success', FlashMessage::translated('flash.results_published_count', ['count' => $updated]));
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
            ->where('is_active', true)
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
     * Show inactive users
     */
    public function inactiveUsers()
    {
        $name = request('name');
        $email = request('email');
        $role = request('role');

        $superAdminEmail = config('app.super_admin_email');

        $users = User::orderBy('created_at', 'desc')
            ->when($superAdminEmail, function ($query) use ($superAdminEmail) {
                $query->where('email', '!=', $superAdminEmail);
            })
            ->where('is_active', false)
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

        return view('admin.users-inactive', ['users' => $users]);
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
            'send_credentials' => 'nullable|boolean',
        ]);

        $user = User::create([
            'title' => $validated['title'],
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'must_change_password' => true,
            'role' => $validated['role'],
            'is_active' => true,
            'email_verified_at' => null,
        ]);

        $token = Str::random(60);
        EmailVerification::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => $token, 'expires_at' => now()->addHours(24), 'verified_at' => null]
        );

        $url = route('verify-email', $token);
        $sendCredentials = $request->boolean('send_credentials');
        $credentials = $sendCredentials ? [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ] : null;
        Mail::to($user->email)->send(new EmailVerificationMail($user, $url, $credentials));

        $createdMessage = $validated['role'] === 'admin' ? 'flash.admin_created' : 'flash.examiner_created';

        return back()->with('success', FlashMessage::translated($createdMessage));
    }

    /**
     * Update a user's role
     */
    public function updateUserRole(\Illuminate\Http\Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', FlashMessage::translated('flash.cannot_change_super_admin_role'));
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,examiner,student',
        ]);

        // Prevent demoting self
        if (auth()->id() === $user->id && $validated['role'] !== 'admin') {
            return back()->with('error', FlashMessage::translated('flash.cannot_change_own_admin_role'));
        }

        $user->role = $validated['role'];
        $user->save();

        return back()->with('success', FlashMessage::translated('flash.user_role_updated'));
    }

    /**
     * Deactivate a user
     */
    public function deactivateUser(User $user)
    {
        if (!auth()->user()->canManageUsers()) {
            return back()->with('error', FlashMessage::translated('messages.user_manage_permission_required'));
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', FlashMessage::translated('messages.user_deactivate_super_admin_forbidden'));
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', FlashMessage::translated('messages.user_deactivate_self_forbidden'));
        }

        $user->forceFill(['is_active' => false])->save();

        return back()->with('success', FlashMessage::translated('messages.user_deactivated'));
    }

    /**
     * Activate a user
     */
    public function activateUser(User $user)
    {
        if (!auth()->user()->canManageUsers()) {
            return back()->with('error', FlashMessage::translated('messages.user_manage_permission_required'));
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', FlashMessage::translated('messages.user_activate_super_admin_forbidden'));
        }

        $user->forceFill(['is_active' => true])->save();

        return back()->with('success', FlashMessage::translated('messages.user_activated'));
    }

    /**
     * Delete a user permanently (with related data)
     */
    public function deleteUser(User $user)
    {
        if (!auth()->user()->canManageUsers()) {
            return back()->with('error', FlashMessage::translated('messages.user_manage_permission_required'));
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', FlashMessage::translated('messages.user_delete_super_admin_forbidden'));
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', FlashMessage::translated('messages.user_delete_self_forbidden'));
        }

        $this->logActivity('user_deleted', User::class, $user->id, "User deleted: {$user->email}");

        DB::transaction(function () use ($user) {
            ExamResult::where('user_id', $user->id)->delete();
            ExamRegistration::where('user_id', $user->id)->delete();
            PaymentHistory::where('user_id', $user->id)->delete();
            EmailVerification::where('user_id', $user->id)->delete();
            ExamExaminerAssignment::where('examiner_id', $user->id)->delete();
            ActivityLog::where('user_id', $user->id)->delete();
            $user->delete();
        });

        return back()->with('success', FlashMessage::translated('messages.user_delete_success'));
    }

    /**
     * Allow an admin to manage users (super admin only)
     */
    public function updateManageUsersPermission(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return back()->with('error', FlashMessage::translated('messages.user_manage_permission_super_admin_only'));
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', FlashMessage::translated('messages.user_manage_permission_super_admin_only'));
        }

        if ($user->role !== 'admin') {
            return back()->with('error', FlashMessage::translated('messages.user_manage_permission_admin_only'));
        }

        $validated = $request->validate([
            'admin_can_manage_users' => 'required|boolean',
        ]);

        $user->forceFill([
            'admin_can_manage_users' => (bool) $validated['admin_can_manage_users'],
        ])->save();

        return back()->with('success', FlashMessage::translated('messages.user_manage_permission_updated'));
    }

    /**
     * Reset super admin password to the value in .env
     */
    public function resetSuperAdminPassword()
    {
        $superAdminEmail = config('app.super_admin_email');
        $superAdminPassword = config('app.super_admin_password');

        if (empty($superAdminEmail) || empty($superAdminPassword)) {
            return back()->with('error', FlashMessage::translated('flash.super_admin_config_missing'));
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

        return back()->with('success', FlashMessage::translated('flash.super_admin_password_reset'));
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
