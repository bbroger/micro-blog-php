<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use App\Infrastructure\Persistence\Post\MySQLPostDAO;
use App\Infrastructure\Persistence\Post\MySQLPostMapper;
use App\Domain\Post\Post;
use App\Domain\Category\Category;
use App\Domain\User\User;

trait PostTestHelper
{
    /**
     * @param Category $category
     * @param User $author
     */
    private function givenAPost(Category $category, User $author): Post
    {
        $post = new Post('title', 'description', 'content');
        $post->setCategory($category);
        $post->setAuthor($author);

        return $post;
    }

    /**
     * @return Post
     */
    private function givenAStoredPost(Category $category, User $author): Post
    {
        $postDAO = $this->givenAPostDAO();
        $post    = $this->givenAPost($category, $author);
        $post->setId($postDAO->store($post));

        return $post;
    }

    /**
     * @return PostDAO
     */
    private function givenAPostDAO(): MySQLPostDAO
    {
        return new MySQLPostDAO($this->gateway);
    }

    /**
     * @return PostDAO
     */
    private function givenAPostMapper(): MySQLPostMapper
    {
        return new MySQLPostMapper($this->gateway);
    }
}
