<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'gym_id' => 1,
                'username' => 'admin',
                'email' => 'admin@stylofitness.com',
                'password' => Hash::make('admin123'),
                'first_name' => 'Administrador',
                'last_name' => 'Sistema',
                'phone' => '+51987654321',
                'date_of_birth' => '1985-01-15',
                'gender' => 'male',
                'role' => 'admin',
                'profile_image' => 'admin-profile.jpg',
                'is_active' => 1,
                'membership_type' => 'premium',
                'membership_expires' => '2025-12-31',
                'preferences' => json_encode([
                    'notifications' => true,
                    'newsletter' => true,
                    'language' => 'es',
                    'theme' => 'light'
                ]),
                'emergency_contact' => json_encode([
                    'name' => 'María Pérez',
                    'phone' => '+51987654322',
                    'relationship' => 'Esposa'
                ]),
                'medical_info' => json_encode([
                    'allergies' => [],
                    'conditions' => [],
                    'medications' => [],
                    'notes' => 'Sin restricciones médicas'
                ]),
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'login_count' => 25,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'gym_id' => 1,
                'username' => 'instructor1',
                'email' => 'carlos.rodriguez@stylofitness.com',
                'password' => Hash::make('instructor123'),
                'first_name' => 'Carlos',
                'last_name' => 'Rodríguez',
                'phone' => '+51987654323',
                'date_of_birth' => '1990-03-20',
                'gender' => 'male',
                'role' => 'instructor',
                'profile_image' => 'carlos-instructor.jpg',
                'is_active' => 1,
                'membership_type' => 'staff',
                'membership_expires' => '2025-12-31',
                'preferences' => json_encode([
                    'notifications' => true,
                    'newsletter' => true,
                    'language' => 'es',
                    'specialties' => ['strength', 'cardio', 'functional']
                ]),
                'emergency_contact' => json_encode([
                    'name' => 'Ana Rodríguez',
                    'phone' => '+51987654324',
                    'relationship' => 'Madre'
                ]),
                'medical_info' => json_encode([
                    'certifications' => ['Personal Trainer', 'Functional Training'],
                    'experience_years' => 5,
                    'specializations' => ['Pérdida de peso', 'Ganancia muscular']
                ]),
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'login_count' => 45,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'gym_id' => 1,
                'username' => 'maria.gonzalez',
                'email' => 'maria.gonzalez@email.com',
                'password' => Hash::make('cliente123'),
                'first_name' => 'María',
                'last_name' => 'González',
                'phone' => '+51987654325',
                'date_of_birth' => '1992-07-10',
                'gender' => 'female',
                'role' => 'client',
                'profile_image' => 'maria-client.jpg',
                'is_active' => 1,
                'membership_type' => 'premium',
                'membership_expires' => '2024-12-31',
                'preferences' => json_encode([
                    'notifications' => true,
                    'newsletter' => true,
                    'language' => 'es',
                    'goals' => ['weight_loss', 'toning'],
                    'preferred_time' => 'morning'
                ]),
                'emergency_contact' => json_encode([
                    'name' => 'Luis González',
                    'phone' => '+51987654326',
                    'relationship' => 'Esposo'
                ]),
                'medical_info' => json_encode([
                    'allergies' => [],
                    'conditions' => [],
                    'medications' => [],
                    'fitness_level' => 'intermediate',
                    'goals' => 'Pérdida de peso y tonificación'
                ]),
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'login_count' => 15,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'gym_id' => 2,
                'username' => 'ana.morales',
                'email' => 'ana.morales@email.com',
                'password' => Hash::make('cliente123'),
                'first_name' => 'Ana',
                'last_name' => 'Morales',
                'phone' => '+51987654327',
                'date_of_birth' => '1988-11-25',
                'gender' => 'female',
                'role' => 'client',
                'profile_image' => 'ana-client.jpg',
                'is_active' => 1,
                'membership_type' => 'basic',
                'membership_expires' => '2024-08-15',
                'preferences' => json_encode([
                    'notifications' => true,
                    'newsletter' => false,
                    'language' => 'es',
                    'goals' => ['fitness', 'health'],
                    'preferred_time' => 'evening'
                ]),
                'emergency_contact' => json_encode([
                    'name' => 'Pedro Morales',
                    'phone' => '+51987654328',
                    'relationship' => 'Hermano'
                ]),
                'medical_info' => json_encode([
                    'allergies' => [],
                    'conditions' => [],
                    'medications' => [],
                    'fitness_level' => 'beginner',
                    'goals' => 'Mantenerse en forma y saludable'
                ]),
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'login_count' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}