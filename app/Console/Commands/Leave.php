<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Leave as Allleaves;
use App\Timeline;
use Carbon\Carbon;
use App\Setting;
use App\User;

class Leave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'APP:Leave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'if employee is absent then add one leave and leave type is uninformed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $users = User::all();

        foreach($users as $user) {

            $user_id = $user->id;
            $name = $user->name;
            $lastWorkingDay = $this->getLastWorkingDay();

            $timeline = Timeline::where('date', $lastWorkingDay)->where('user_id', $user_id)->exists();

            $leaves = Allleaves::where('leave_date', $lastWorkingDay)->where('user_id', $user_id)->exists();

            if (!$timeline && !$leaves) {

                $leave = Allleaves::create([
                    'user_id' => $user_id,
                    'detail' => $name." ".Allleaves::$DETAIL,
                    'isTracked' => 1,
                    'isFull' => 1,
                    'leave_type' => Allleaves::$UNINFORMED,
                    'leave_date' => $lastWorkingDay,
                ]);
            }
        }

        echo "leave added successfully";
    }
    private function getLastWorkingDay()
    {
        //get weekday, from 0 (sunday) to 6 (saturday)
        $currentWeekDay = date("w");

        $formate = "Y-m-d";

        switch ($currentWeekDay) {
            case "1":
                {  // monday
                    $lastWorkingDay = date($formate, strtotime("-3 day"));
                    break;
                }
            case "0":
                {  // sunday
                    $lastWorkingDay = date($formate, strtotime("-2 day"));
                    break;
                }
            default:
                {  //all other days
                    $lastWorkingDay = date($formate, strtotime("-1 day"));
                    break;
                }
        }

        return $lastWorkingDay;
    }

}
