<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class Post extends Model
{
    use HasApiTokens, SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'user_id',
    ];

    // relation one-to-many: a user can have many posts.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
