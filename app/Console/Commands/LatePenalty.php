<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Timeline;
use App\Leave;
use Carbon\Carbon;
use App\Setting;
use App\CreditPoint;

class LatePenalty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'APP:LatePenalty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'if employee come 3 days late in week, then add one leave and add -1 point in its credit points ';

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

        $office_timing = Setting::getValue('office_time');

        $office_timing = "$office_timing:00";

        foreach ($users as $user) {

            $user_id = $user->id;
            $name = $user->name;

            $today = Carbon::today()->toDateString();

            $lastWorkingDay = $this->getLastWorkingDay(Carbon::parse($today));
            $secondLastWorkingDay = $this->getLastWorkingDay(Carbon::parse($lastWorkingDay));
            $thirdLastWorkingDay = $this->getLastWorkingDay(Carbon::parse($secondLastWorkingDay));

            $lastTimeLine = Timeline::where([
                ['user_id', $user_id],
                ['date', '=', $lastWorkingDay],
                ['login_time', '>', $office_timing]
            ])->orderBy('login_time', 'ASC')->first();

            if ($lastTimeLine) {

                $secondTimeLine = Timeline::where([
                    ['user_id', $user_id],
                    ['date', '=', $secondLastWorkingDay],
                    ['login_time', '>', $office_timing]
                ])->orderBy('login_time', 'ASC')->first();

                if ($secondTimeLine) {

                    $thirdTimeLine = Timeline::where([
                        ['user_id', $user_id],
                        ['date', '=', $thirdLastWorkingDay],
                        ['login_time', '>', $office_timing]
                    ])->orderBy('login_time', 'ASC')->first();

                    if ($thirdTimeLine) {

                        $leave = Leave::create([
                            'user_id' => $user_id,
                            'detail' => $name . " " . Leave::$LATEPENALTY,
                            'isTracked' => 1,
                            'isFull' => 1,
                            'leave_type' => Leave::$UNINFORMED,
                            'leave_date' => $lastWorkingDay,
                        ]);
                        $credit_point = CreditPoint::create([
                            'user_id' => $user_id,
                            'sources' => Timeline::$LATEPENALTY,
                            'points' => Setting::getValue('latepenalty'),
                        ]);

                        echo "leave and credit points are added for user";
                    }
                }
            }
        }
    }

    function getLastWorkingDay($date)
    {
        $currentWeekDay = $date->dayOfWeek;

        $formate = "Y-m-d";

        switch ($currentWeekDay) {
            case "1":
                {  // monday
                    $lastWorkingDay = $date->subDay(3);
                    break;
                }
            case "0":
                {  // sunday
                    $lastWorkingDay = $date->subDay(2);
                    break;
                }
            default:
                {  //all other days
                    $lastWorkingDay = $date->subDay(1);
                    break;
                }
        }
        return $lastWorkingDay;
    }
}
