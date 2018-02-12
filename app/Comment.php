<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class Comment extends Model
{
    //
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function toggleComment()
    {
        if ($this->status == 0)
        {
            $this->status = 1;
        }
        else
        {
            $this->status = 0;
        }
    }

    public function remove()
    {
        $this->delete();
    }

    public static function add($comment, $postId)
    {
        if ($comment != null)
        {
            $com = new Comment();
            $com->text = $comment;
            $com->post_id = $postId;
            $com->user_id = Auth::user()->id;
            $com->status = 1;
            $com->save();
        }
    }

    public static function updateForChecked($statuses, $checkIds=null, $changeStatus=null)
    {
        if ($checkIds != null) {
            foreach ($checkIds as $id => $checked) {
                $statuses[$id] = $changeStatus;
            }
        }
        foreach ($statuses as $id => $status) {

            $comment = Comment::find($id);
            $comment->status = $status;
            $comment->save();
        }
    }

    public static function getStatuses()
    {
        return [0 => 'Спам', 1 => 'На проверке', 2 => 'Опубликован'];
    }
}
