<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Volunteer;
use Carbon\Carbon;

class VolunteersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrCreate([
            'name' => 'Hans Muster',
            'email' => 'hans.muster@gmail.com',
        ]);
        $volunteer = Volunteer::create([
            'first_name' => 'Hans',
            'last_name' => 'Muster',
            'street' => 'Musterstrasse 1',
            'zip' => '1234',
            'city' => 'Musterort',
            'country' => 'Germany',
            'nationality' => 'German',
            'date_of_birth' => '1995-01-02',
            'gender' => 'male',
            'phone' => '+491234567890',
            'whatsapp' => '+491234567890',
            'skype' => 'hans.muster',
            'professions' => 'Teacher',
            'other_skills' => 'Construction',
            'language_skills' => 'German, English',
            'previous_experience' => 'Teaching German courses in refugee reception centers.',
            'passport_no' => 'C1234567',
            'user_id' => $user->id,
        ]);
        // Applied trip
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->addDays(100),
            'departure' => Carbon::now()->addDays(120),
            'status' => 'applied',
        ]);
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->addDays(150),
            'departure' => Carbon::now()->addDays(180),
            'status' => 'applied',
        ]);
        // Denied trip
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->addDays(40),
            'departure' => Carbon::now()->addDays(50),
            'status' => 'denied',
        ]);
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->addDays(35),
            'departure' => Carbon::now()->addDays(37),
            'status' => 'denied',
        ]);
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->subDays(2),
            'departure' => Carbon::now()->subDays(5),
            'status' => 'denied',
        ]);
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->subDays(150),
            'departure' => Carbon::now()->subDays(160),
            'status' => 'denied',
        ]);
        // Approved trip
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->addDays(50),
            'departure' => Carbon::now()->addDays(70),
            'status' => 'approved',
        ]);
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->addDays(10),
            'departure' => Carbon::now()->addDays(20),
            'status' => 'approved',
        ]);
        // Current trip
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->subDays(7),
            'departure' => Carbon::now()->addDays(14),
            'status' => 'approved',
        ]);
        // Past trip
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->subDays(50),
            'departure' => Carbon::now()->subDays(30),
            'status' => 'approved',
        ]);
        $volunteer->trips()->create([
            'arrival' => Carbon::now()->subDays(120),
            'departure' => Carbon::now()->subDays(100),
            'status' => 'approved',
        ]);
    }
}
