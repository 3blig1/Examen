<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelectedModuleScoresTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_only_sees_and_saves_scores_for_selected_modules(): void
    {
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
            'amount_paid' => 50,
            'selected_modules' => ['reading', 'speaking'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.exams.results', $exam))
            ->assertOk()
            ->assertSee('name="reading_score"', false)
            ->assertSee('name="speaking_score"', false)
            ->assertDontSee('name="writing_score"', false)
            ->assertDontSee('name="listening_score"', false);

        $this->post(route('admin.results.store', $registration), [
            'reading_score' => 80,
            'speaking_score' => 90,
            // Extra fields sent by a modified client must not add unselected modules.
            'writing_score' => 100,
            'listening_score' => 100,
            'status' => 'passed',
        ])->assertRedirect();

        $this->assertDatabaseHas('exam_results', [
            'exam_registration_id' => $registration->id,
            'reading_score' => 80,
            'speaking_score' => 90,
            'writing_score' => null,
            'listening_score' => null,
            'total_score' => 85,
        ]);
    }
}
