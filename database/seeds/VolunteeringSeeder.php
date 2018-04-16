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
            'order' => 0,
        ]);
        $mid = VolunteerJobCategory::create([
            'title' => [
                'en' => 'Mid-term Volunteer (3-8 Weeks)',
                'de' => 'Mid-term-VolontärIn (3 – 8 Wochen)'
            ],
            'order' => 1,
        ]);
        $long = VolunteerJobCategory::create([
            'title' => [
                'en' => 'Long-term Volunteer (more than two months)',
                'de' => 'Long-term-VolontärIn (mind. 2 Monate)'
            ],
            'order' => 2,
        ]);
        $project = VolunteerJobCategory::create([
            'title' => [
                'en' => 'Project Volunteer',
                'de' => 'Projekt-VolontärIn'
            ],
            'order' => 3,
        ]);

        $short->jobs()->create([
            'title' => [
                'en' => 'Short-term Volunteer',
                'de' => 'Short-term-Freiwillige/r'
            ],
            'description' => [
                'en' => 'A short-term volunteer trip is a great opportunity for anyone on a tight schedule. Even in your two to three weeks our community center will offer you a broad insight into humanitarian work. You will support our helpers and get to know their different projects such as the bank, the clothing boutique, the kids’ space and the café. For more information see our volunteer information sheet.',
                'de' => 'Dein Kalender ist schon voll, aber dein Herz schlägt für die Freiwilligenarbeit? In unserem Gemeinschaftszentrum hast du auch mit zwei oder drei Wochen die Möglichkeit, einen breiten Einblick in die humanitäre Arbeit zu erhalten. Als Kurzzeit-Freiwillige/r unterstützt du unsere Helpers und lernst die verschiedenen Projekte kennen wie die Bank, die Kleider-Boutique, das Kinderzimmer und das Café. Genauere Informationen findest du in unseren Freiwilligeninformationen!'
            ],
            'available_dates' => [
                'en' => 'year-round',
                'de' => 'jederzeit'
            ],
            'minimum_stay' => [
                'en' => '2 weeks',
                'de' => 'zwei Wochen'
            ],
            'requirements' => [
                'en' => 'at least 20 years old',
                'de' => 'mindestalter 20 Jahre'
            ],
            'order' => 0,

        ]);
        
    }
}
