<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesSeeder::class);
        $this->call(ProjectsSeeder::class);
        $this->call(CouponTypesSeeder::class);
        $this->call(VolunteeringSeeder::class);

        $this->call(CalendarResourceSeeder::class);
        $this->call(PersonsTableSeeder::class);
    }
}
