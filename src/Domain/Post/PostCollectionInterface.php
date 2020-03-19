<?php
declare(strict_types=1);

namespace App\Domain\Post;

use App\Domain\Category\Category;

interface PostCollectionInterface
{
    /**
     * @param Post $post
     */
    public function add(Post $post): void;

    /**
     * @param int $id
     * @throws Exception
     */
    public function getPostById(int $id): Post;

    /**
     * @param Category $category
     * @throws Exception
     */
    public function getPostsByCategory(Category $category): ?PostCollection;
}
