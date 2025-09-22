<?php

namespace App\Repositories;

use App\Models\Like;

class LikeRepository
{
    public function add(array $data): Like
    {
        return Like::firstOrCreate([
            'user_id' => $data['user_id'],
            'likeable_id' => $data['likeable_id'],
            'likeable_type' => $data['likeable_type'],
        ]);
    }
}