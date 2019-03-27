<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Project;
use Carbon\Carbon;

class ProjectsTableSeeder extends Seeder
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

            DB::table('projects')->insert([
                'company_id' => $faker->numberBetween($min = 1, $max = 2),
                'start_date' =>$today->addYears($year),
                'end_date' =>$today->addYears($year),
                'progress' => $faker->numberBetween($min = 1, $max = 100),
                'detail_file' => "seeds/$rand.txt" ,
                'prof_and_loss' => "seeds/$rand.txt" ,
                'status' => $faker->randomElement($array = array('ongoing', 'completed', 'upcoming', 'canceled', 'pused')),
                'client_comment' => $faker->text,
                'ceo_comment' => $faker->text,
                'name' => $faker->name,
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }
    }
}