<?php

require_once __DIR__ . '/../../app/Config/Database.php';

use StyleFitness\Config\Database;

class DatabaseSeeder
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo "Starting database seeding...\n";
        
        try {
            $this->call('GymsSeeder');
            $this->call('UsersSeeder');
            $this->call('SpecialOffersSeeder');
            $this->call('WhyChooseUsSeeder');
            $this->call('TestimonialsSeeder');
            $this->call('LandingPageConfigSeeder');
            $this->call('FeaturedProductsConfigSeeder');
            
            echo "Database seeding completed successfully!\n";
        } catch (Exception $e) {
            echo "Error during seeding: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    /**
     * Call a seeder class
     *
     * @param string $seederClass
     * @return void
     */
    private function call($seederClass)
    {
        echo "Running {$seederClass}...\n";
        
        $seederFile = __DIR__ . '/' . $seederClass . '.php';
        if (file_exists($seederFile)) {
            require_once $seederFile;
            $seeder = new $seederClass();
            if (method_exists($seeder, 'run')) {
                $seeder->run();
                echo "{$seederClass} completed.\n";
            } else {
                echo "Warning: {$seederClass} does not have a run() method.\n";
            }
        } else {
            echo "Warning: {$seederFile} not found.\n";
        }
    }
}