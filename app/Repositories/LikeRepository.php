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

    public function getLikes(array $data)
    {
        return Like::where('likeable_id', $data['likeable_id'])
            ->where('likeable_type', $data['likeable_type'])
            ->with('user:id,fullname')
            ->get();
    }

    public function remove(array $data)
    {
        return Like::where('user_id', $data['user_id'])
            ->where('likeable_id', $data['likeable_id'])
            ->where('likeable_type', $data['likeable_type'])
            ->delete();
    }
}
