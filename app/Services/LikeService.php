<?php

namespace App\Services;

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
