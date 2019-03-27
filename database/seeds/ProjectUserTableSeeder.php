<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\ProjectUser;
use Carbon\Carbon;

class ProjectUserTableSeeder extends Seeder
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
            $year = rand(-1,0);
            $today = Carbon::now();
            $created = $today->addDays($rand);

            DB::table('project_user')->insert([
                'user_id' =>$faker->numberBetween($min = 1, $max =50),
                'project_id' =>$faker->numberBetween($min = 1, $max =50),
                'user_remarks' => $faker->text,
                'created_at' => $created,
                'updated_at' =>$created,
	        ]);
	}
    } 
}