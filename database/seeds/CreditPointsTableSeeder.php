<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\CreditPoint;
use Carbon\Carbon;

class CreditPointsTableSeeder extends Seeder
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
            
            DB::table('credit_points')->insert([
                'points' =>$faker->randomElement($array = array('1', '2', '-1')),
                'user_id' =>$faker->numberBetween($min = 1, $max =50),
                'sources' => $faker->word,
                'created_at' =>  $created ,
                'updated_at' =>  $created ,
	        ]);
	}
    } 
}