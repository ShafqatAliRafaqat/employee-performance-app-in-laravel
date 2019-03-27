<?php
use App\NewsFeed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class NewsFeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run() {

        $faker = Faker::create();

        foreach (range(1, 50) as $index) {

            $rand = rand(0,15);
            $today = Carbon::now();
            $created = $today->addDays($rand);

            DB::table('news_feeds')->insert([
                'news' => $faker->text,
                'created_at' => $created,
                'updated_at' => $created,
            ]);

        }
    }
}