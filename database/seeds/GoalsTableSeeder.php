<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Goal;
use Carbon\Carbon;

class GoalsTableSeeder extends Seeder
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
           
            $randYear = rand(-1,0);
            $rand = rand(0,15);
            $today = Carbon::now();
            $year = $today->addYears($randYear);
            $created = $today->addDays($rand);

            DB::table('goals')->insert([
                'project_id' =>$faker->numberBetween($min = 1, $max =10),
                'start_date' => $year,
                'end_date' => $year,
                'file' => "seeds/$rand.txt" ,
                'submission_time' =>$today->addDays($rand),
                'status' =>$faker->randomElement($array = array('ongoing','completed','upcoming','canceled','pused')),
                'ceo_comment' =>$faker->text,
                'description' => $faker->text,
                'created_at' =>  $created ,
                'updated_at' =>  $created ,
	        ]);
	}
    } 
}