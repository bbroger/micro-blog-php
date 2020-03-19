<?php
declare(strict_types=1);

namespace App\Domain\Comment;

use App\Domain\Entity\Collection;
use App\Domain\Post\Post;

class CommentCollection extends Collection implements CommentCollectionInterface
{
    /**
     * @param Comment $comment
     */
    public function add(Comment $comment): void
    {
        parent::append($comment);
    }

    /**
     * @param int $id
     */
    public function getCommentById(int $id): Comment
    {
        $filter = fn($comment) => $comment->getId() === $id;
        $data   = parent::filter($filter);

        return array_pop($data->getAggregates());
    }

    /**
     * @param Post $post
     */
    public function getCommentsByPost(Post $post): self
    {
        $filter = fn($comment) => $comment->getPost()->getId() === $post->getId();
        return parent::filter($filter);
    }
}
