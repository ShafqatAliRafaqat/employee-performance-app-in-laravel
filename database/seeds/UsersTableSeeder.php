<?php

use Illuminate\Database\Seeder;
use App\User;
use Faker\Factory;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        $users = 50;

        $faker = Factory::create();
       
        for( $j = 1; $j<=$users; $j++ ) {
           
            $rand = rand(0,15);
            $year = rand(-1,0);
            $today = Carbon::now();
            $created = $today->addDays($rand);
           
            $user = User::create([
                'name' => $faker->name,
                'email' => ($j==1) ? "admin@admin.com": $faker->email,
                'remember_token' => str_random(10),
                'cnic' => $faker->creditCardNumber,
                'phone' => $faker->e164PhoneNumber,
                'joining' => $today->addDays($rand) ,
                'employee_type' =>  $faker->randomElement($array = array('technical','business')),
                'cv' =>  "seeds/$rand.txt",
                'address' => $faker->address,
                'leaves_allowed' =>$faker->randomDigit,
                'company_id' => $faker->numberBetween($min = 1, $max =2),
                'password' => bcrypt('123456'),
                'created_at' => $created,
                'updated_at' => $created,
           ]);

           $role = ($j==1) ? env('SUPER_ADMIN_ROLE_NAME','Admin'): "Employee";

           $user->assignRole($role);

        }

        // for( $i = 1; $i<=$users; $i++ ) {
        //     UserDetails::create([
        //        //...
        //     ]);
        // }
        
    }
}
