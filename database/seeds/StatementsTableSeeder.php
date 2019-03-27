<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Statement;
use App\User;
use Carbon\Carbon;

class StatementsTableSeeder extends Seeder
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

            DB::table('statements')->insert([
                'company_id' =>$faker->numberBetween($min = 1, $max =2),
                'month' =>$faker->month ,
                'year' =>$faker->year,
                'quadrant' =>$faker->company ,
                'file' =>  "seeds/$rand.txt",
                'created_at' =>$created,
                'updated_at' => $created,
	        ]);
	}
    } 
}