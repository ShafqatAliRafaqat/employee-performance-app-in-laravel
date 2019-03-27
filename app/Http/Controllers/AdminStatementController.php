<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Statement;
use App\Http\Resources\StatementResource as StatementResource;
use Illuminate\Support\Facades\File;
use App\Helpers\FileHelper;
use App\Helpers\QB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminStatementController extends Controller
{
    protected $permissions = [
        'index' => 'statement-list',
        'create' => 'statement-create',
        'update' => 'statement-update',
        'delete' => 'statement-delete',
    ];

    public function index(Request $request)
    {

        $input = $request->all();

        $qb = Statement::orderBy('created_at','DESC')->with('Company');

        $qb = QB::where($input, "company_id", $qb);
        $qb = QB::where($input, "year", $qb);
        $qb = QB::where($input, "month", $qb);

        $statements = $qb->paginate();

        return StatementResource::collection($statements);

    }

    private function getQuator($t_month)
    {

        if ($t_month <= 3) {
            return $quadrant = '-Q1 (Jan - Mar)';
        } elseif ($t_month > 3 && $t_month <= 6) {
            return $quadrant = '-Q2 (Apr - Jun)';
        } elseif ($t_month > 6 && $t_month <= 9) {
            return $quadrant = '-Q3 (Jul - Sep)';
        } elseif ($t_month > 9 && $t_month <= 12) {
            return $quadrant = '-Q4 (Oct - Dec)';
        }

        return $quadrant;
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $this->validateOrAbort($data, [
            'year' => 'required',
            'month' => 'required',
            'file' => 'required',
            'company_id' => 'required'
        ]);

        $t_month = $data['month'];

        $getQuator = $this->getQuator($t_month);

        $quadrant = $data['year'] . $getQuator;

        $file = $request->file('file');

        $restult = FileHelper::saveFile($file, "statementFiles");

        $statement = Statement::create([
            'year' => $data['year'],
            'month' => $data['month'],
            'file' => $restult,
            'quadrant' => $quadrant,
            'company_id' => $data['company_id']
        ]);

        return [
            'message' => __('messages.model.create.success', [
                'model' => __('messages.Statement')
            ]),
            'data' => StatementResource::make($statement)
        ];

    }

    public function update(Request $request, $id)
    {
        $statement = Statement::find($id);
        
        if (!$statement) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Statement')
            ]));
        }

        $file_path = $statement->file;
        
        $data = $request->all();

        $this->validateOrAbort($data, [
            'year' => 'required',
            'month' => 'required',
            'company_id' => 'required'
        ]);

        $t_month = $data['month'];

        $getQuator = $this->getQuator($t_month);

        $quadrant = $data['year'] . $getQuator;

        if ($request->file('file')) {

            $file = $request->file('file');

            FileHelper::deleteFileIfNotDefault($file_path);

            $file_path = FileHelper::saveFile($file, "statementFiles");
        
        }

        $statement->update([
            'year' => $data['year'],
            'month' => $t_month,
            'file' => $file_path,
            'quadrant' => $quadrant,
            'company_id' => $data['company_id']
        ]);


        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.Statement')
            ]),
            'data' => StatementResource::make($statement)
        ];
    }

    public function delete(Statement $statement, $id)
    {
        $statement =  Statement::find($id);
        
        if (!$statement) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Statement')
            ]));
        } 
        FileHelper::deleteFileIfNotDefault($statement->cv);
        
        $statement->delete();
        
        return [
            'message' => __('messages.model.delete.success', [
                'model' => __('messages.Statement')
            ])
        ];
    }

}
