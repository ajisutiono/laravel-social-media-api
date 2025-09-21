<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Post;

class CommentRepository
{
    public function store($postId, array $data)
    {
        $post = Post::findOrFail($postId);

        $data['post_id'] = $post->id;

        return Comment::create($data);
    }

    public function getAll($postId)
    {
        $post = Post::findOrFail($postId);
        return $post->comments()->with('user')->get();
    }

    public function getId($postId, $commentId)
    {
        $post = Post::findOrFail($postId);

        $comment = $post->comments()
            ->where('id', $commentId)
            ->with('user')
            ->firstOrFail();
        return $comment;
    }

    public function update($commentId, array $data)
    {
        $comment = Comment::findOrFail($commentId);

        $comment->update($data);

        return $comment->load('user');
    }

    public function delete($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        $comment->delete();

        return $comment;
    }
}
