<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\GoalUser;
use Carbon\Carbon;

class GoalUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
    	foreach (range(1,50) as $index) {
           
            $rand = rand(0,15);
            $today = Carbon::now();
            $created = $today->addDays($rand);

            DB::table('goal_user')->insert([
                'user_id' =>$faker->numberBetween($min = 1, $max =50),
                'goal_id' =>$faker->numberBetween($min = 1, $max =50),
                'user_remarks' => $faker->text,
                'created_at' =>$created,
                'updated_at' => $created,
	        ]);
	}
    } 
}