<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamRegistration;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Show all available exams
     */
    public function index()
    {
        Exam::refreshStatuses();

        $exams = Exam::where('status', 'upcoming')
            ->where('date', '>', now())
            ->where(function ($query) {
                $query->whereNull('registration_deadline')
                      ->orWhere('registration_deadline', '>=', now());
            })
            ->orderBy('date')
            ->paginate(12);

        return view('exams.index', ['exams' => $exams]);
    }

    /**
     * Show exam details
     */
    public function show(Exam $exam)
    {
        Exam::refreshStatuses();

        $user = auth()->user();
        $isRegistered = $user
            ? $user->examRegistrations()->where('exam_id', $exam->id)->exists()
            : false;

        return view('exams.show', [
            'exam' => $exam,
            'isRegistered' => $isRegistered,
        ]);
    }

    /**
     * Register for exam
     */
    public function register(Request $request, Exam $exam)
    {
        Exam::refreshStatuses();

        $user = auth()->user();

        // Check if already registered
        if (ExamRegistration::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->exists()) {
            return back()->with('error', 'You are already registered for this exam');
        }

        // Check if exam has available spots
        if (!$exam->isAvailable()) {
            return back()->with('error', 'This exam is no longer available for registration');
        }

        // Validate selected modules
        $validated = $request->validate([
            'selected_modules' => 'required|array|min:1',
            'selected_modules.*' => 'in:reading,writing,listening,speaking',
        ]);

        // Calculate amount based on exam type
        $amount = 0;
        if ($exam->exam_type === 'complete') {
            $amount = $exam->price;
        } else {
            // Module exam - calculate based on number of modules selected
            $moduleCount = count($validated['selected_modules']);
            $amount = ($moduleCount === 4)
                ? $exam->price
                : $moduleCount * $exam->price_per_module;
        }

        // Create registration
        $registration = ExamRegistration::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'selected_modules' => $validated['selected_modules'],
            'amount_paid' => $amount,
        ]);

        // Increment registered count
        $exam->increment('registered_count');

        return back()->with('success', 'Registration successful! Please complete your payment.');
    }

    /**
     * Cancel registration
     */
    public function cancelRegistration(ExamRegistration $registration)
    {
        // Allow owner or admin to cancel
        if (! auth()->user() || (! auth()->user()->isAdmin() && $registration->user_id !== auth()->id())) {
            abort(403);
        }

        if ($registration->status === 'completed') {
            return back()->with('error', 'Cannot cancel a completed registration');
        }

        $registration->update(['status' => 'cancelled']);
        $registration->exam->decrement('registered_count');

        return back()->with('success', 'Registration cancelled successfully');
    }

    /**
     * Confirm payment
     */
    public function confirmPayment(Request $request, ExamRegistration $registration)
    {
        // Only admins should mark payments as paid. Regular users cannot call this.
        if (! auth()->user() || ! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount_paid' => 'nullable|numeric|min:0.01',
        ]);

        $registration->update([
            'payment_status' => 'paid',
            'amount_paid' => $validated['amount_paid'] ?? 0,
            'payment_date' => now(),
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Payment recorded by admin and registration confirmed.');
    }
}
