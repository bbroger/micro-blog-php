<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use App\Infrastructure\Persistence\Comment\MySQLCommentMapper;
use App\Infrastructure\Persistence\Comment\MySQLCommentDAO;
use App\Domain\Comment\CommentCollection;
use App\Domain\Comment\Comment;
use App\Domain\Category\Category;
use App\Domain\Post\Post;
use App\Domain\User\User;

interface MySQLPostMapperInterface 
{
    /**
     * @param Post $post
     * @param MySQLPostDAO $postDAO
     * @param MySQLCommentDAO $commentDAO
     */
    public function store(
        Post $post, 
        MySQLPostDAO $postDAO, 
        MySQLCommentDAO $commentDAO
    ): int;

    /**
     * @param Post $post
     * @throws Exception
     */
    public function getCategory(Post $post): Category;

    /**
     * @param Post $post
     * @param UserDAO $userDAO
     */
    public function getAuthor(Post $post): User;

    /**
     * @param Post $post
     * @throws Exception
     * @return CommentCollection
     */
    public function getComments(Post $post, MySQLCommentMapper $mapper): CommentCollection;
}
