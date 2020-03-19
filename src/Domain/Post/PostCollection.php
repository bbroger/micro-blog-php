<?php
declare(strict_types=1);

namespace App\Domain\Post;

use App\Domain\Category\Category;
use App\Domain\Entity\Collection;
use \Exception;

class PostCollection extends Collection implements PostCollectionInterface
{
    /**
     * @param Post $post
     */
    public function add(Post $post): void
    {
        parent::append($post);
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function getPostById(int $id): Post
    {
        if (!$this->count()) {
            throw new Exception('There are no registered posts.');
        }
        
        $criteria = fn($post) => $post->getId() === $id;
        $filtered = parent::filter($criteria);

        if (!$filtered->count()) {
            throw new Exception(sprintf('Post with id %d not found.', $id));
        }
        
        return array_pop($filtered->getAggregates());
    }

    /**
     * @param Category $category
     * @throws Exception
     */
    public function getPostsByCategory(Category $category): ?self
    {
        if (!$this->count()) {
            throw new Exception('There are no registered posts.');
        }

        $criteria = fn($post) => $post->getCategory()->getId() === $category->getId();
        $filtered = parent::filter($criteria);

        if (!$filtered->count()) {
            throw new Exception(sprintf(
                'There are no posts registered in the category of id %d', 
                $category->getId()
            ));
        }

        return $filtered;
    }
}
