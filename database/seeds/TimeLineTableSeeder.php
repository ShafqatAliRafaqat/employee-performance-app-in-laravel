<?php
use App\Timeline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TimeLineTableSeeder extends Seeder
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

            DB::table('timelines')->insert([
                'login_time' => $faker->time($format = 'H:i:s', $max = 'now'),
                'logout_time' => $faker->time($format = 'H:i:s', $max = 'now'),
                'date' => $today->addDays($rand),
                'user_id' => $faker->numberBetween($min = 1, $max = 50),
                'created_at' =>$created,
                'updated_at' => $created,
            ]);
        }
    }
}