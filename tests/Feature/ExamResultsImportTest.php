<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExamResultsImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_scores_only_for_modules_selected_by_each_candidate(): void
    {
        $admin = $this->createSuperAdmin();
        $student = User::factory()->create([
            'name' => 'Student User',
            'first_name' => 'Student',
            'last_name' => 'User',
            'date_of_birth' => '1995-03-14',
            'role' => 'student',
            'is_active' => true,
        ]);
        $exam = $this->createExam();
        $registration = ExamRegistration::create([
            'user_id' => $student->id,
            'exam_id' => $exam->id,
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'amount_paid' => 50,
            'selected_modules' => ['reading', 'speaking'],
        ]);
        $csv = implode("\n", [
            'first_name;last_name;date_of_birth;selected_modules;reading_score;listening_score;writing_score;speaking_score;level_achieved;status;remarks',
            'Student;User;' . \PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel(new \DateTimeImmutable('1995-03-14')) . ';reading|speaking;80;;;90;B1;passed;Imported result',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.exams.results.import', $exam), [
                'results_file' => UploadedFile::fake()->createWithContent('scores.csv', $csv),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('exam_results', [
            'exam_registration_id' => $registration->id,
            'reading_score' => 80,
            'listening_score' => null,
            'writing_score' => null,
            'speaking_score' => 90,
            'total_score' => 85,
            'level_achieved' => 'B1',
            'status' => 'passed',
        ]);
    }

    public function test_import_rejects_row_when_candidate_identity_does_not_match_registration(): void
    {
        $admin = $this->createSuperAdmin();
        $student = User::factory()->create([
            'name' => 'Student User',
            'first_name' => 'Student',
            'last_name' => 'User',
            'date_of_birth' => '1995-03-14',
            'role' => 'student',
            'is_active' => true,
        ]);
        $exam = $this->createExam();
        $registration = ExamRegistration::create([
            'user_id' => $student->id,
            'exam_id' => $exam->id,
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'amount_paid' => 50,
            'selected_modules' => ['reading'],
        ]);
        $csv = implode("\n", [
            'first_name,last_name,date_of_birth,selected_modules,reading_score,listening_score,writing_score,speaking_score,level_achieved,status,remarks',
            'Student,Wrong,1995-03-14,reading,80,,,,A1,passed,',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.exams.results', $exam))
            ->post(route('admin.exams.results.import', $exam), [
                'results_file' => UploadedFile::fake()->createWithContent('scores.csv', $csv),
            ])
            ->assertRedirect(route('admin.exams.results', $exam))
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('exam_results', 0);
    }

    public function test_import_rejects_ambiguous_candidate_identity(): void
    {
        $admin = $this->createSuperAdmin();
        $exam = $this->createExam();
        $registrations = collect();

        foreach (['one@example.com', 'two@example.com'] as $email) {
            $student = User::factory()->create([
                'name' => 'Alex Candidate',
                'first_name' => 'Alex',
                'last_name' => 'Candidate',
                'date_of_birth' => '1990-04-20',
                'email' => $email,
                'role' => 'student',
                'is_active' => true,
            ]);
            $registrations->push(ExamRegistration::create([
                'user_id' => $student->id,
                'exam_id' => $exam->id,
                'status' => 'confirmed',
                'payment_status' => 'unpaid',
                'amount_paid' => 50,
                'selected_modules' => ['reading'],
            ]));
        }

        $csv = implode("\n", [
            'first_name,last_name,date_of_birth,selected_modules,reading_score,listening_score,writing_score,speaking_score,level_achieved,status,remarks',
            'Alex,Candidate,1990-04-20,reading,80,,,,A1,passed,',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.exams.results', $exam))
            ->post(route('admin.exams.results.import', $exam), [
                'results_file' => UploadedFile::fake()->createWithContent('scores.csv', $csv),
            ])
            ->assertRedirect(route('admin.exams.results', $exam))
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('exam_results', 0);
    }

    public function test_import_accepts_registration_export_columns_without_status(): void
    {
        $admin = $this->createSuperAdmin();
        $student = User::factory()->create([
            'name' => 'Student User',
            'first_name' => 'Student',
            'last_name' => 'User',
            'date_of_birth' => '1995-03-14',
            'role' => 'student',
            'is_active' => true,
        ]);
        $exam = $this->createExam();
        $registration = ExamRegistration::create([
            'user_id' => $student->id,
            'exam_id' => $exam->id,
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'amount_paid' => 50,
            'selected_modules' => ['reading', 'writing'],
        ]);
        $csv = implode("\n", [
            'Prüfungsstufe;Titel Vor;last_name;first_name;Titel Nach;Geschlecht;Geburts-Ort;Geburts-Land;Nationalität;date_of_birth;Prüfer/In Nr 1 schriftlich;Prüfer/In Nr 2 schriftlich;Prüfer/In Nr 1 mündlich;Prüfer/In Nr 2 mündlich;reading_score;listening_score;writing_score;speaking_score',
            implode(';', [
                'B1', '', 'User', 'Student', '', '', '', '', '', '1995-03-14',
                '', '', '', '', '81', '', '91', '',
            ]),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.exams.results.import', $exam), [
                'results_file' => UploadedFile::fake()->createWithContent('registration-export.csv', $csv),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('exam_results', [
            'exam_registration_id' => $registration->id,
            'reading_score' => 81,
            'writing_score' => 91,
            'listening_score' => null,
            'speaking_score' => null,
            'total_score' => 86,
            'status' => 'pending',
        ]);
    }

    public function test_regular_admin_cannot_see_or_use_import_features(): void
    {
        config(['app.super_admin_email' => 'super-admin@example.com']);
        $admin = User::factory()->create([
            'name' => 'Regular Admin',
            'email' => 'regular-admin@example.com',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $exam = $this->createExam();

        $this->actingAs($admin)
            ->get(route('admin.exams.results', $exam))
            ->assertOk()
            ->assertDontSee('results_file')
            ->assertDontSee(__('messages.results_import_title'));

        $this->get(route('admin.exams.results.template', $exam))->assertForbidden();
        $this->post(route('admin.exams.results.import', $exam), [])->assertForbidden();
    }

    private function createExam(): Exam
    {
        return Exam::create([
            'name' => 'B1',
            'description' => 'Test exam',
            'date' => now()->addDays(10),
            'max_participants' => 20,
            'registered_count' => 1,
            'price' => 100,
            'location' => 'CIAL Sokodé',
            'status' => 'upcoming',
        ]);
    }

    private function createSuperAdmin(): User
    {
        $email = 'super-admin@example.com';
        config(['app.super_admin_email' => $email]);

        return User::factory()->create([
            'name' => 'Super Admin',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => $email,
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
