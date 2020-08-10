<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Profile extends Model
{
    

    protected $fillable=['job','picture','rate','user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
