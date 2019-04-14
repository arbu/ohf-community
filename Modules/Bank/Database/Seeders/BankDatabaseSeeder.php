<?php

namespace Modules\Bank\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BankDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(CouponTypesSeeder::class);
        $this->call(ProjectsSeeder::class);
    }
}