<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use App\Domain\Post\Post;

interface PostDAOInterface
{
    /**
     * @param Post $post
     */
    public function store(Post $post): int;

    /**
     * @param int $id
     * @throws Exception
     */
    public function findById(int $id): Post;

    /**
     * @param Post $post
     */
    public function update(Post $post): int;

    /**
     * @param Post $post
     */
    public function destroy(Post $post): int;
}
