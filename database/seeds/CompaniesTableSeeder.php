<?php
use App\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        
    	foreach (range(1,2) as $index) {
            $rand = rand(0,15);
            $today = Carbon::now();
            $created = $today->addDays($rand);

	        DB::table('companies')->insert([
	            'name' =>$faker->company ,
                'description' => $faker->text,
                'created_at' =>  $created,
                'updated_at' =>$created,
	      ]);
	}
    } 
}
