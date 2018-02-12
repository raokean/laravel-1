<?php

namespace App\Http\Controllers\User;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request,[
            'comment' => 'required',
            'post_id' => 'required',
        ]);

        Comment::add($request->get('comment'), $request->get('post_id'));
        return redirect()->back()->with('message', 'Ваша комментария вскоре будет опубликован');
    }
}
