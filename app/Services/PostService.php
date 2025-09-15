<?php

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function createPost(array $data)
    {
        $data['user_id'] = Auth::id();

        if (isset($data['image'])) {
            $path = $data['image']->store('posts', 'public');
            $data['image'] = $path;
        }

        return $this->postRepository->store($data);
    }
}