<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Volunteer;
use App\VolunteerJob;
use App\VolunteerJobCategory;

class VolunteeringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $short = VolunteerJobCategory::create([
            'title' => [
                'en' => 'Short-term Volunteer (2-3 Weeks)',
                'de' => 'Short-term-VolontärIn (2 – 3 Wochen)'
            ],
        ]);
        $mid = VolunteerJobCategory::create([
            'title' => [
                'en' => 'Mid-term Volunteer (3-8 Weeks)',
                'de' => 'Mid-term-VolontärIn (3 – 8 Wochen)'
            ],
        ]);
        $long = VolunteerJobCategory::create([
            'title' => [
                'en' => 'Long-term Volunteer (more than two months)',
                'de' => 'Long-term-VolontärIn (mind. 2 Monate)'
            ],
        ]);
        $project = VolunteerJobCategory::create([
            'title' => [
                'en' => 'Project Volunteer',
                'de' => 'Projekt-VolontärIn'
            ],
        ]);

        
    }
}
