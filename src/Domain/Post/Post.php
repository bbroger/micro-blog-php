<?php
declare(strict_types=1);

namespace App\Domain\Post;

use App\Domain\Entity\Identifiable;
use App\Domain\Entity\Identifier as Identifier;
use App\Domain\Category\Categorizable;
use App\Domain\Category\Categoryzer as Categoryzer;
use App\Domain\User\User;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentCollection;
use \InvalidArgumentException;
use \LengthException;

class Post implements Identifiable, Categorizable
{
    use Identifier;
    use Categoryzer;
    
    private string $title;
    
    private string $description;
    
    private string $content;

    private User $author;

    private CommentCollection $comments;

    public const MAX_TITLE_LENGTH = 255;

    public const MAX_DESCRIPTION_LENGTH = 255;

    public function __construct(string $title, string $description, string $content)
    {
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setContent($content);
        $this->comments = new CommentCollection();
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
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return CommentCollection
     */
    public function getComments(): CommentCollection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     */
    public function add(Comment $comment): void
    {
        $this->comments->add($comment);
    }

    /**
     * @param string $title
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
     * @param string $description
     * @throws InvalidArgumentException
     * @throws LengthException
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

    /**
     * @param string $content
     * @throws InvalidArgumentException
     */
    public function setContent(string $content): void
    {
        if (empty($content)) {
            throw new InvalidArgumentException('The content must be filled.');
        }

        $this->content = $content;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }
}
