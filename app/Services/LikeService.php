<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Repositories\LikeRepository;

class LikeService
{
    protected $likeRepositroy;

    public function __construct(LikeRepository $likeRepository)
    {
        $this->likeRepositroy = $likeRepository;
    }

    public function addLike(array $data)
    {
        $model = $data['likeable_type'] === Post::class
                ? Post::find($data['likeable_id'])
                : Comment::find($data['likeable_id']);

        if (! $model) {
            abort(404, 'Likeable not found');
        }
        
        return $this->likeRepositroy->add($data);
    }

    public function showLikes(array $data)
    {
        return $this->likeRepositroy->getLikes($data);
    }

    public function removeLike(array $data)
    {
        return $this->likeRepositroy->remove($data);
    }
}
