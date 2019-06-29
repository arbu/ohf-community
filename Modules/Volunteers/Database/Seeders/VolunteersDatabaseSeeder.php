<?php

namespace Modules\Volunteers\Database\Seeders;

use Modules\Volunteers\Entities\Volunteer;
use Modules\Volunteers\Entities\Stay;

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

        factory(Volunteer::class, 150)->create()->each(function($d){
            $d->stays()->saveMany(factory(Stay::class, mt_rand(0, 3))->make());
        });

    }
}
