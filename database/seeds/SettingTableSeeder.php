<?php

use Illuminate\Database\Seeder;

use App\Setting;
use Carbon\Carbon;


class SettingTableSeeder extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run(){
        
        $settings = [
            ['name'=>'Allowed Leaves','key' => 'leaves_allowed','value' => 10],
            ['name'=>'total Working Hours','key' => 'total_working_hours','value' =>9],
            ['name'=>'office Time','key' => 'office_time','value' =>"10:15"],
            ['name'=>'Credit points for Goal completed before date','key' => 'goal_completed_before ','value' =>2],
            ['name'=>'Credit points for Goal completed in date ','key' => 'goal_completed','value' =>1],
            ['name'=>'Credit points for Goal completed after date ','key' => 'goal_completed_after','value' =>-1],
            ['name'=>'employee come three day late in office','key' => 'latepenalty ','value' =>-1],
        ];

        foreach ($settings as $setting){
            Setting::create($setting);
        }
        
    }
}
