<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \Storage;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function savePassword($password)
    {
        if ($password != null) {
            $this->password = bcrypt($password);
            $this->save();
        }
    }

    protected function removeAvatar()
    {
        if($this->avatar != null) {
            Storage::delete('uploads/avatars/' . $this->avatar);
        }
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public static function add($fields)
    {
        $user = new static;
        $user->fill($fields);
        $user->password = bcrypt($fields['password']);
        $user->save();

        return $user;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->savePassword($fields['password']);
        $this->save();
    }

    public function remove()
    {
        $this->removeAvatar();
        $this->delete();
    }

    public function uploadAvatar($image)
    {
        if($image == null) { return; }

        $this->removeAvatar();
        $filename = str_random(10).'.'.$image->extension();
        $image->storeAs('uploads/avatars/', $filename);
        $this->avatar = $filename;
        $this->save();
    }

    public function getAvatar()
    {
        $baseUrl = url('/');
        if ($this->avatar == null)
        {
            return $baseUrl . '/uploads/avatars/no-image.png';
        }
        return $baseUrl .'/uploads/avatars/'.$this->avatar;
    }

    public function makeAdmin()
    {
        $this->is_admin = 1;
        $this->save();
    }

    public function makeNormal()
    {
        $this->is_admin = 0;
        $this->save();
    }

    public function toggleAdmin($level)
    {
        if (is_numeric($level))
        {
            $this->is_admin = $level;
        }
        else return;
    }

    public function setBan($time)
    {
        if (is_numeric($time))
        {
            $this->status = $this->asDateTime($time);
            $this->save();
        }
    }

    public function generatePassword($password)
    {
        if ($password != null)
        {
            $this->password = bcrypt($password);
            $this->save();
        }
    }

    public static function getUsers($param = null)
    {
        $res;
        switch ($param) {
            case 'ddl': $res = User::pluck('name', 'id')->all(); break;
            default: $res = User::all(); break;
        }
        return $res;
    }
}
