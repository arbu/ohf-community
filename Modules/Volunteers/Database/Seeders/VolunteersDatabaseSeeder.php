<?php

namespace Modules\Volunteers\Database\Seeders;

use Modules\Volunteers\Entities\Volunteer;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class VolunteersDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        factory(Volunteer::class, 150)->create();
    }
}
