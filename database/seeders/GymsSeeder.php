<?php

require_once __DIR__ . '/../../app/Config/Database.php';

use StyleFitness\Config\Database;

class GymsSeeder
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $currentTime = date('Y-m-d H:i:s');
            
            $gyms = [
            [
                'name' => 'STYLOFITNESS Lima Centro',
                'address' => 'Av. Javier Prado Este 1234, San Isidro, Lima',
                'phone' => '+51 1 234-5678',
                'email' => 'lima@stylofitness.com',
                'logo' => 'logo-lima.png',
                'theme_colors' => json_encode([
                    'primary' => '#FF6B35',
                    'secondary' => '#2C3E50',
                    'accent' => '#F39C12',
                    'background' => '#FFFFFF',
                    'text' => '#2C3E50'
                ]),
                'settings' => json_encode([
                    'currency' => 'PEN',
                    'timezone' => 'America/Lima',
                    'language' => 'es',
                    'max_members' => 500,
                    'booking_advance_days' => 7
                ]),
                'operating_hours' => json_encode([
                    'monday' => ['open' => '06:00', 'close' => '22:00'],
                    'tuesday' => ['open' => '06:00', 'close' => '22:00'],
                    'wednesday' => ['open' => '06:00', 'close' => '22:00'],
                    'thursday' => ['open' => '06:00', 'close' => '22:00'],
                    'friday' => ['open' => '06:00', 'close' => '22:00'],
                    'saturday' => ['open' => '07:00', 'close' => '20:00'],
                    'sunday' => ['open' => '08:00', 'close' => '18:00']
                ]),
                'social_media' => json_encode([
                    'facebook' => 'https://facebook.com/stylofitness',
                    'instagram' => 'https://instagram.com/stylofitness',
                    'youtube' => 'https://youtube.com/stylofitness',
                    'tiktok' => 'https://tiktok.com/@stylofitness'
                ]),
                'is_active' => 1,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'STYLOFITNESS Miraflores',
                'address' => 'Av. Larco 456, Miraflores, Lima',
                'phone' => '+51 1 234-5679',
                'email' => 'miraflores@stylofitness.com',
                'logo' => 'logo-miraflores.png',
                'theme_colors' => json_encode([
                    'primary' => '#FF6B35',
                    'secondary' => '#2C3E50',
                    'accent' => '#E74C3C',
                    'background' => '#FFFFFF',
                    'text' => '#2C3E50'
                ]),
                'settings' => json_encode([
                    'currency' => 'PEN',
                    'timezone' => 'America/Lima',
                    'language' => 'es',
                    'max_members' => 300,
                    'booking_advance_days' => 5
                ]),
                'operating_hours' => json_encode([
                    'monday' => ['open' => '05:30', 'close' => '23:00'],
                    'tuesday' => ['open' => '05:30', 'close' => '23:00'],
                    'wednesday' => ['open' => '05:30', 'close' => '23:00'],
                    'thursday' => ['open' => '05:30', 'close' => '23:00'],
                    'friday' => ['open' => '05:30', 'close' => '23:00'],
                    'saturday' => ['open' => '06:00', 'close' => '21:00'],
                    'sunday' => ['open' => '07:00', 'close' => '19:00']
                ]),
                'social_media' => json_encode([
                    'facebook' => 'https://facebook.com/stylofitness.miraflores',
                    'instagram' => 'https://instagram.com/stylofitness_miraflores',
                    'youtube' => 'https://youtube.com/stylofitness',
                    'whatsapp' => '+51987654321'
                ]),
                'is_active' => 1,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ];
        
        foreach ($gyms as $gym) {
            $sql = "INSERT INTO gyms (name, address, phone, email, logo, theme_colors, settings, operating_hours, social_media, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                $gym['name'],
                $gym['address'],
                $gym['phone'],
                $gym['email'],
                $gym['logo'],
                $gym['theme_colors'],
                $gym['settings'],
                $gym['operating_hours'],
                $gym['social_media'],
                $gym['is_active'],
                $gym['created_at'],
                $gym['updated_at']
            ]);
        }
        
        echo "GymsSeeder: Gimnasios insertados correctamente.\n";
        
        } catch (Exception $e) {
            echo "Error en GymsSeeder: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}