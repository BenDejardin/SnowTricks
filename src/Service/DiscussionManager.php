<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Reply;
use App\Entity\Tricks;
use App\Entity\Discussions;
use Doctrine\ORM\EntityManagerInterface;

class DiscussionManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createDiscussion(Discussions $discussion, User $author, Tricks $trick): void
    {
        $discussion->setAuthor($author);
        $discussion->setIdTricks($trick);
        $discussion->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($discussion);
        $this->entityManager->flush();
    }

    public function createReply(Reply $reply, User $author, Discussions $discussion): void
    {
        // Logique pour créer une nouvelle réponse
        $reply->setAuthor($author);
        $reply->setDiscussion($discussion);

        $this->entityManager->persist($reply);
        $this->entityManager->flush();
    }
}
