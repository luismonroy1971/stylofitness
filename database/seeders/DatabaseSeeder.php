<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            GymsSeeder::class,
            UsersSeeder::class,
            SpecialOffersSeeder::class,
            WhyChooseUsSeeder::class,
            TestimonialsSeeder::class,
            LandingPageConfigSeeder::class,
            FeaturedProductsConfigSeeder::class,
        ]);
    }
}