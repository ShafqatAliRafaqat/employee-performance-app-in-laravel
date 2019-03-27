<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $permissions = [];

    public function __construct() {
        foreach ($this->permissions as $m => $p){
            $this->middleware("permission:$p", ['only' => [$m]]);
        }
    }

    protected function validateOrAbort($input,$rules){
        $validator = Validator::make($input,$rules);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
    }
    
}
