<?php
use App\Leave;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class LeaveTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 50) as $index) {
            
            $rand = rand(0,15);
            $year = rand(-1,0);
            $today = Carbon::now();
            $created = $today->addDays($rand);
           
            DB::table('leaves')->insert([
                'leave_date' => $today->addYears($year),
                'isTracked' =>  $faker->numberBetween($min = 0, $max = 1),
                'isFull' =>  $faker->numberBetween($min = 0, $max = 1),
                'leave_type' => $faker->randomElement($array = array('Informed','Uninformed')),
                'detail' => $faker->word,
                'user_id' => $faker->numberBetween($min = 1, $max = 50),
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }
    }
}