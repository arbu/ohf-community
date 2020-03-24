<?php

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
        $this->call(UserRolesSeeder::class);
        $this->call(AccountingDatabaseSeeder::class);
        $this->call(FundraisingDatabaseSeeder::class);
        $this->call(CalendarResourceSeeder::class);
        $this->call(WikiArticleTableSeeder::class);
        $this->call(PeopleDatabaseSeeder::class);
        $this->call(CouponTypesSeeder::class);
        $this->call(HelpersDatabaseSeeder::class);
        $this->call(LibraryDatabaseSeeder::class);
    }
}
