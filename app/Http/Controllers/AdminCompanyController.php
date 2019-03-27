<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\QB;
use App\Http\Resources\CompanyResource as CompanyResource;

class AdminCompanyController extends Controller
{
    protected $permissions = [
        'index' => 'company-list',
        'store' => 'company-create',
        'update' => 'company-update',
        'destroy' => 'company-delete',
    ];

    public function index(Request $request)
    {
        $input = $request->all();

        $qb = Company::orderBy('created_at', 'DESC');
        $qb = QB::where($input, "id", $qb);
        $qb = QB::whereLike($input, "name", $qb);
        $qb = QB::whereLike($input, "description", $qb);

        $company = $qb->paginate();

        return CompanyResource::collection($company);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $this->validateOrAbort($data, [
            'name' => 'required',
            'description' => 'required'
        ]);

        $company = Company::create([

            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return [
            'message' => __('messages.model.create.success', [
                'model' => __('messages.Company')
            ]),
            'data' => CompanyResource::make($company)
        ];
    }
    public function update(Request $request, $id, Company $company)
    {
        $company = Company::find($id);
        if (!$company) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Company')
            ]));
        }

        $data = $request->all();

        $this->validateOrAbort($data, [
            'name' => 'required',
            'description' => 'required'
        ]);

        $company->update([

            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.Company')
            ]),
            'data' => CompanyResource::make($company)
        ];
    }
    public function delete(Company $company, $id)
    {
        $company = Company::find($id);
        if (!$company) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Company')
            ]));
        }
        $company->delete();
        return [
            'message' => __('messages.model.delete.success', [
                'model' => __('messages.Company')
            ])
        ];
    }
}
