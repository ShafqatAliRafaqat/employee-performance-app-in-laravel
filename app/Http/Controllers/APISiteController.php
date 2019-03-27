<?php

namespace App\Http\Controllers;

use App\Http\Resources\APISiteResource;
use Illuminate\Http\Request;
use App\Goal;
use App\Project;
use App\User;
use App\Leave;

class APISiteController extends Controller
{
 
    public function metaData()
    {

        return [
            'data' => [
                'goal_status' => $this->getKeyValue(Goal::$STATUS),
                'employee_types' => $this->getKeyValue(User::$EMPLOYEE_TYPES),
                'leave_type' => $this->getKeyValue(Leave::$LEAVE_TYPES),
                'isFull' => $this->getKeyValue(Leave::$ISFULL),
                'project_status' => $this->getKeyValue(Project::$STATUS)
            ]
        ];
    }

    private function getKeyValue($array)
    {

        $elements = [];

        if ($array) {
            foreach ($array as $key => $value) {
                $elements[] = [
                    'key' => $key,
                    'value' => $value
                ];
            }
        }

        return $elements;
    }
}
