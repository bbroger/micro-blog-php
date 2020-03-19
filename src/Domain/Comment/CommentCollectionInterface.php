<?php
declare(strict_types=1);

namespace App\Domain\Comment;

use App\Domain\Post\Post;

interface CommentCollectionInterface
{
    /**
     * @param Comment $comment
     */
    public function add(Comment $comment): void;

    /**
     * @param int $id
     */
    public function getCommentById(int $id): ?Comment;

    /**
     * @param Post $post
     */
    public function getCommentsByPost(Post $post): ?self;
}
