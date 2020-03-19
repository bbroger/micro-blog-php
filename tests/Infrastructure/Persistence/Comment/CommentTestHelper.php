<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Comment;

use App\Infrastructure\Persistence\Comment\MySQLCommentDAO;
use App\Infrastructure\Persistence\Comment\MySQLCommentMapper;
use App\Domain\Comment\Comment;
use App\Domain\User\User;
use App\Domain\Post\Post;

trait CommentTestHelper
{
    /**
     * @param User $user
     * @param Post $post
     */
    private function givenAComment(User $user, Post $post = null): Comment
    {
        $comment = new Comment('test');
        $comment->setUser($user);
        
        if ($post) {
            $comment->setPost($post);
        }

        return $comment;
    }

    /**
     * @return Comment
     */
    private function givenAStoredComment(User $user, Post $post): Comment
    {
        $commentDAO = $this->givenACommentDAO();
        $comment    = $this->givenAComment($user, $post);
        $comment->setId($commentDAO->store($comment));

        return $comment;
    }

    /**
     * @return CommentDAO
     */
    private function givenACommentDAO(): MySQLCommentDAO
    {
        return new MySQLCommentDAO($this->gateway);
    }

    /**
     * @return MySQLCommentMapper
     */
    private function givenACommentMapper(): MySQLCommentMapper 
    {
        return new MySQLCommentMapper($this->gateway);
    }
}
