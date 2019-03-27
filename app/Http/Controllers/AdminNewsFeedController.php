<?php

namespace App\Http\Controllers;

use App\NewsFeed;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\QB;
use App\Http\Resources\NewsFeedResource as NewsFeedResource;

class AdminNewsFeedController extends Controller
{
    protected $permissions = [
        'index' => 'news-list',
        'store' => 'news-create',
        'update' => 'news-update',
        'destroy' => 'news-delete',
    ];

    public function index(Request $request)
    {
        $input = $request->all();

        $qb = NewsFeed::orderBy('updated_at', 'DESC');

        $qb = QB::where($input, "id", $qb);
        $qb = QB::whereLike($input, "news", $qb);

        $NewsFeed = $qb->paginate(5);

        return NewsFeedResource::collection($NewsFeed);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $this->validateOrAbort($data, [
            'news' => 'required',
        ]);

        $NewsFeed = NewsFeed::create([

            'news' => $data['news'],
        ]);

        return [
            'message' => __('messages.model.create.success', [
                'model' => __('messages.News')
            ]),
            'data' => NewsFeedResource::make($NewsFeed)
        ];
    }
    
    public function update(Request $request, $id, NewsFeed $NewsFeed)
    {
        $NewsFeed = NewsFeed::find($id);
        if (!$NewsFeed) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.News')
            ]));
        }

        $data = $request->all();

        $this->validateOrAbort($data, [
            'news' => 'required',
        ]);

        $NewsFeed->update([

            'news' => $data['news'],
        ]);

        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.News')
            ]),
            'data' => NewsFeedResource::make($NewsFeed)
        ];
    }

    public function delete(NewsFeed $NewsFeed, $id)
    {
        $NewsFeed = NewsFeed::find($id);
        if (!$NewsFeed) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.News')
            ]));
        }
        $NewsFeed->delete();
        return [
            'message' => __('messages.model.delete.success', [
                'model' => __('messages.News')
            ])
        ];
    }
}
