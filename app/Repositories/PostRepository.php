<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    public function store(array $data)
    {
        return Post::create($data);
    }

    public function getAll()
    {
        return Post::all();
    }

    public function getId($postId)
    {
        return Post::findOrFail($postId);
    }

    public function update($postId, array $data)
    {
        $post = Post::findOrFail($postId);
        $post->update($data);

        return $post;
    }

    public function delete($postId)
    {
        $post = Post::findOrFail($postId);
        $post->delete();

        return $post;
    }
}
