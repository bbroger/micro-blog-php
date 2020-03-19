<?php
declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Entity\Identifiable;
use App\Domain\Entity\Identifier as Identifier;
use App\Domain\Category\Categorizable;
use App\Domain\Category\Categoryzer as Categoryzer;
use App\Domain\Post\PostCollection;
use App\Domain\Post\Post;
use \InvalidArgumentException;
use \LengthException;

class Category implements Identifiable, Categorizable
{
    use Identifier;
    use Categoryzer;

    private string $title;

    private string $description;

    private PostCollection $posts;

    public const MAX_TITLE_LENGTH = 150;

    public const MAX_DESCRIPTION_LENGTH = 255;

    public function __construct(string $title, string $description)
    {
        $this->setTitle($title);
        $this->setDescription($description);
        $this->posts = new PostCollection();
    }

    /**
     * @param Post $post
     */
    public function add(Post $post): void
    {
        $this->posts->add($post);
    }

    /**
     * @return PostCollection
     */
    public function getPosts(): PostCollection
    {
        return $this->posts;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @throws InvalidArgumentException
     * @throws LengthException
     */
    public function setTitle(string $title): void
    {
        if (empty($title)) {
            throw new InvalidArgumentException('The title must be filled.');
        }

        if (mb_strlen($title) > self::MAX_TITLE_LENGTH) {
            $message = sprintf(
                'The title must have a maximum of %d characters.', 
                self::MAX_TITLE_LENGTH
            );

            throw new LengthException($message);
        }

        $this->title = $title;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setDescription(string $description): void
    {
        if (empty($description)) {
            throw new InvalidArgumentException('The description must be filled.');
        }

        if (mb_strlen($description) > self::MAX_DESCRIPTION_LENGTH) {
            $message = sprintf(
                'The description must have a maximum of %d characters.', 
                self::MAX_DESCRIPTION_LENGTH
            );

            throw new LengthException($message);
        }

        $this->description = $description;
    }
}
