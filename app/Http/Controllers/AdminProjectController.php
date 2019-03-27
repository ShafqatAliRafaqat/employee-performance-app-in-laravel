<?php

namespace App\Http\Controllers;

use App\Project;
use App\Company;
use App\User;
use App\ProjectUser;
use App\Helpers\FileHelper;
use App\Helpers\QB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProjectResource as ProjectResource;

class AdminProjectController extends Controller
{
    protected $permissions = [
        'index' => 'project-list',
        'showSingleProjectDetails' => 'project-showSingleProjectDetails',
        'create' => 'project-create',
        'update' => 'project-update',
        'delete' => 'project-delete',
    ];

    public function index(Request $request)
    {

        $input = $request->all();

        $qb = Project::orderBy('created_at', 'DESC')->with(['Users', 'Company']);

        $qb = QB::whereLike($input, "name", $qb);
        $qb = QB::whereBetween($input, "start_date", $qb);
        $qb = QB::whereBetween($input, "end_date", $qb);
        $qb = QB::where($input, "status", $qb);
        $qb = QB::where($input, "company_id", $qb);
        $qb = QB::whereLike($input, "client_comment", $qb);
        $qb = QB::whereLike($input, "ceo_comment", $qb);

        $projects = $qb->paginate();

        return ProjectResource::collection($projects);
    }

    public function showSingleProjectDetails($id)
    {
        $project = Project::where('id', $id)->with(['Company', 'Users'])->first();

        return ProjectResource::make($project);

    }
    public function create(Request $request)
    {
        $data = $request->all();

        $this->validateOrAbort($data, [
            'detail_file' => 'required',
            'prof_and_loss' => 'required',
            'name' => 'required',
            'end_date' => 'required',
            'start_date' => 'required',
            'progress' => 'required',
            'status' => 'required',
            'company_id' => 'required',
            'client_comment' => 'required',
            'ceo_comment' => 'required',
            'employees' => 'required'
        ]);

        $file = $request->file('detail_file');

        $restult = FileHelper::saveFile($file, "Project_detail_files");

        $file1 = $request->file('prof_and_loss');

        $restult1 = FileHelper::saveFile($file1, "Project_prof_and_loss_files");

        $project = Project::create([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'detail_file' => $restult,
            'prof_and_loss' => $restult1,
            'progress' => $data['progress'],
            'status' => $data['status'],
            'company_id' => $data['company_id'],
            'client_comment' => $data['client_comment'],
            'ceo_comment' => $data['ceo_comment']
        ]);

        $employees = explode(",", $data['employees']);
      
        $project->Users()->sync($employees);

        $project =  Project::where('id', $project->id)->with(['Users', 'Company'])->first();
        return [
            'message' => __('messages.model.create.success', [
                'model' => __('messages.Project')
            ]),
            'data' => ProjectResource::make($project)
        ];
    }

    public function update(Request $request, $id)
    {

        $project = Project::where('id', $id)->with(['Users', 'Company'])->first();

        if (!$project) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Project')
            ]));
        }

        $file_path = $project->detail_file;
        $file_path1 = $project->prof_and_loss;

        $data = $request->all();

        $this->validateOrAbort($data, [
            'name' => 'required',
            'end_date' => 'required',
            'start_date' => 'required',
            'progress' => 'required',
            'status' => 'required',
            'company_id' => 'required',
            'client_comment' => 'required',
            'ceo_comment' => 'required',
            'employees' => 'required'
        ]);

        if ($request->file('detail_file')) {

            $file = $request->file('detail_file');

            FileHelper::deleteFileIfNotDefault($file_path);

            $file_path = FileHelper::saveFile($file, "Project_detail_files");

        }
        if ($request->file('prof_and_loss')) {

            $file1 = $request->file('prof_and_loss');

            FileHelper::deleteFileIfNotDefault($file_path1);

            $file_path1 = FileHelper::saveFile($file1, "Project_prof_and_loss_files");

        }
        $project->update([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'detail_file' => $file_path,
            'prof_and_loss' => $file_path1,
            'progress' => $data['progress'],
            'status' => $data['status'],
            'company_id' => $data['company_id'],
            'client_comment' => $data['client_comment'],
            'ceo_comment' => $data['ceo_comment']
        ]);

        $employees = explode(",", $data['employees']);
        $project->Users()->sync($employees);

        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.Project')
            ]),
            'data' => ProjectResource::make($project)
        ];

    }

    public function delete(Project $project, $id)
    {
        $project = Project::find($id);

        if (!$project) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Project')
            ]));
        }

        FileHelper::deleteFileIfNotDefault($project->detail_file);

        FileHelper::deleteFileIfNotDefault($project->prof_and_loss);

        $project->delete();

        return [
            'message' => __('messages.model.delete.success', [
                'model' => __('messages.Project')
            ])
        ];
    }
}
