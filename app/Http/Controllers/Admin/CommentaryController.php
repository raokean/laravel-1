<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentaryController extends Controller
{
    public function index()
    {
        $comments = Comment::all();
        $statuses = Comment::getStatuses();
        return view('admin.comment.index', compact('comments', 'statuses'));
    }

    public function store(Request $request)
    {
        if (empty($request->get('check_ids'))) {
            $this->validate($request, [
                'statuses' => 'required',
            ]);
            Comment::updateForChecked(
                $request->get('statuses')
            );
        }
        else {
            $this->validate($request, [
                'check_ids' => 'required',
                'statuses' => 'required',
                'changeStatus' => 'required',
            ]);
            Comment::updateForChecked(
                $request->get('statuses'),
                $request->get('check_ids'),
                $request->get('changeStatus')
            );
        }
        return redirect()->back()->with('message', 'Изменение успешно сохранен');
    }
}
