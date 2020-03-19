<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Comment;

use App\Domain\Comment\Comment;
use App\Domain\User\User;

interface MySQLCommentMapperInterface 
{
    /**
     * @param Comment $comment
     * @throws Exception
     */
    public function getUser(Comment $comment): User;
}
