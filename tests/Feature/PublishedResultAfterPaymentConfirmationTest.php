<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamRegistration;
use App\Models\ExamResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublishedResultAfterPaymentConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_published_result_after_admin_confirms_pending_payment(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $student = User::factory()->create([
            'name' => 'Student User',
            'first_name' => 'Student',
            'last_name' => 'User',
            'role' => 'student',
            'is_active' => true,
        ]);
        $exam = Exam::create([
            'name' => 'B1',
            'description' => 'Test exam',
            'date' => now()->addDays(10),
            'max_participants' => 20,
            'registered_count' => 1,
            'price' => 100,
            'location' => 'CIAL Sokodé',
            'status' => 'upcoming',
        ]);
        $registration = ExamRegistration::create([
            'user_id' => $student->id,
            'exam_id' => $exam->id,
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'amount_paid' => 100,
            'selected_modules' => ['reading', 'writing', 'listening', 'speaking'],
        ]);
        $result = ExamResult::create([
            'exam_registration_id' => $registration->id,
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'reading_score' => 80,
            'listening_score' => 80,
            'writing_score' => 80,
            'speaking_score' => 80,
            'total_score' => 80,
            'level_achieved' => 'B1',
            'status' => 'passed',
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.registrations.confirm', $registration))
            ->assertRedirect();

        $this->assertDatabaseHas('exam_registrations', [
            'id' => $registration->id,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);
        $this->assertSame('paid', $result->fresh()->registration->payment_status);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('results.show', $result));

        $this->get(route('results.show', $result))
            ->assertOk()
            ->assertSee('B1');
    }
}