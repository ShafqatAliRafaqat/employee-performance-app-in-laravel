<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SettingResource;


class AdminSettingsController extends Controller {
    
    protected $permissions = [
        'index'=>'setting-list',
        'edit' =>'setting-edit'
    ];

    public function index(){
        $settings = Setting::get();
        return SettingResource::collection($settings);
    }

    public function edit(Request $request){

        $input = $request->all();
 
        $this->validateOrAbort($input,[
            'settings' => 'present'
        ]);
 
        foreach ($input['settings'] as $s){
            Setting::where('key',$s['key'])->update([
                    'value' => $s['value']
            ]);
        }
 
        return response()->json([
            'message' => __("messages.model.edit.success",[
                'model' => __('messages.Setting')
            ])
        ]);
    }
}
