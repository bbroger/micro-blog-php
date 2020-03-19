<?php
declare(strict_types=1);

namespace App\Domain\Comment;

use App\Domain\Entity\Identifiable;
use App\Domain\Entity\Identifier as Identifier;
use App\Domain\User\User;
use App\Domain\Post\Post;
use \InvalidArgumentException;
use \LengthException;
use \stdClass;

class Comment implements Identifiable
{
    use Identifier;

    private string $comment;

    private User $user;

    private Post $post;

    public const MAX_COMMENT_LENGTH = 255;

    public function __construct(string $comment)
    {
        $this->setComment($comment);
    }

    /**
     * @return string
     */
    public function getComment(): string 
    {
        return $this->comment;
    }

    /**
     * @return User
     */
    public function getUser(): User 
    {
        return $this->user;
    }

    /**
     * @return Post
     */
    public function getPost(): Post 
    {
        return $this->post;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        if (empty($comment)) {
            throw new InvalidArgumentException('The comment must be filled.');
        }

        if (mb_strlen($comment) > self::MAX_COMMENT_LENGTH) {
            throw new LengthException(sprintf(
                'The comment must have a maximum of %d characters.', 
                self::MAX_COMMENT_LENGTH
            ));
        }

        $this->comment = $comment;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void 
    {
        $this->user = $user;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void 
    {
        $this->post = $post;
    }
}
