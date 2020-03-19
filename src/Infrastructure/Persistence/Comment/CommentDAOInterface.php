<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Comment;

use App\Domain\Comment\Comment;

interface CommentDAOInterface
{
    /**
     * @param Comment $comment
     */
    public function store(Comment $comment): int;

    /**
     * @param int $id
     * @throws Exception
     */
    public function findById(int $id): Comment;

     /**
     * @param Comment $comment
     */
    public function update(Comment $comment): int;

    /**
     * @param Comment $comment
     */
    public function destroy(Comment $comment): int;
}
