<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function run() {
        
        $this->call(RolesAndPermissionsTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CreditPointsTableSeeder::class);
        $this->call(LeaveTableSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(GoalsTableSeeder::class);
        $this->call(GoalUserTableSeeder::class);
        $this->call(ProjectUserTableSeeder::class);
        $this->call(StatementsTableSeeder::class);
        $this->call(TimeLineTableSeeder::class);
        $this->call(NewsFeedTableSeeder::class);
        
    }
}