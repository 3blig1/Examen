<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Exam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'phone' => '+43 1 234 5678',
            'country' => 'Austria',
            'date_of_birth' => '1990-01-01',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create examiner user
        User::create([
            'name' => 'Examiner User',
            'first_name' => 'Examiner',
            'last_name' => 'User',
            'email' => 'examiner@example.com',
            'phone' => '+43 1 234 5679',
            'country' => 'Austria',
            'date_of_birth' => '1985-02-15',
            'password' => Hash::make('password123'),
            'role' => 'examiner',
            'email_verified_at' => now(),
        ]);

        // Create test students
        User::factory(10)->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create sample exams
        $examLevels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
        
        foreach ($examLevels as $index => $level) {
            Exam::create([
                'name' => $level,
                'description' => "ÖSD Examen Niveau $level - Testen Sie Ihre Deutschkenntnisse mit unserem standardisierten Examen.",
                'date' => now()->addDays(10 + ($index * 7))->setTime(9, 0),
                'max_participants' => 30,
                'registered_count' => 0,
                'price' => 80 + ($index * 10),
                'location' => 'Vienna Exam Center',
                'status' => 'upcoming',
            ]);
        }
    }
}
