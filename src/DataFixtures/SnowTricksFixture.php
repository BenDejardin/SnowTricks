<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Group;
use App\Entity\Reply;
use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Videos;
use App\Entity\Discussions;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class SnowTricksFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groups = [
            [
                'name' => 'Figures de Base',
            ],
            [
                'name' => 'Sauts',
            ],
            [
                'name' => 'Grabs',
            ],
            [
                'name' => 'Rotations',
            ],
            [
                'name' => 'Inversions',
            ],
            [
                'name' => 'Rails et Boxes',
            ],
            [
                'name' => 'Halfpipe',
            ],
            [
                'name' => 'Slopestyle',
            ],
            [
                'name' => 'Freeride',
            ],
            [
                'name' => 'Freestyle Backcountry',
            ],
            [
                'name' => 'Big Air',
            ],
            [
                'name' => 'Old School',
            ],
        ];
        

        $users = [
            [
                'email' => 'bendejardin8@gmail.com',
                'password' => '$2y$13$Vhpw1z4aizyEbe4gbDRHueGJV/3/QoK3bgzHHP1/f7tbNKyTzuzbe',
                'isverified' => 1,
                'username' =>  'BenDejardin',
                'avatar' => '65043f931f932.png'
            ]
        ];

        $tricks = [
            [
                'group_id' => 1,
                'name' => 'Ollie',
                'description' => 'L\'ollie est un saut de base où le rider fait décoller la planche en appuyant sur le tail (arrière de la planche) tout en soulevant ses genoux. C\'est le mouvement fondamental pour sauter sur des obstacles et réaliser d\'autres tricks.',
                'created_by_id' => 1,
                'created_at' => new \DateTime('2023-09-15 14:41:40'),
                'slug' => 'Ollie',
            ],
            [
                'group_id' => 3,
                'name' => 'Indy Grab',
                'description' => 'L\'Indy Grab consiste à saisir le carre frontside de la planche avec la main arrière tout en étant en l\'air. Le rider plie ses genoux pour atteindre la planche tout en gardant le pied avant attaché.',
                'created_by_id' => 1,
                'created_at' => new \DateTime('2023-09-15 15:41:21'),
                'slug' => 'Indy-Grab',
            ],
            [
                'group_id' => 4,
                'name' => '360 Tail Grab',
                'description' => 'Cette figure implique une rotation de 360 degrés (un tour complet) tout en saisissant le tail de la planche en l\'air. Le rider tourne autour de son axe vertical tout en maintenant le grab.',
                'created_by_id' => 1,
                'created_at' => new \DateTime('2023-09-15 15:20:40'),
                'slug' => '360-Tail-Grab',
            ],
        ];

        $images = [
            [
                'path' => '650450eef2c94.jpg',
                'alt' => 'Image sur le trick Ollie',
                'trick_id' => 1,
            ],
            [
                'path' => '6504510486d9f.jpg',
                'alt' => 'Image sur le trick Ollie',
                'trick_id' => 1,
            ],
            [
                'path' => '650451c2916bf.jpg',
                'alt' => 'Image sur le trick Indy Grab',
                'trick_id' => 2,
            ],
            [
                'path' => '65045287561e2.jpg',
                'alt' => 'Image sur le trick 360 Tail Grab',
                'trick_id' => 3,
            ],
            [
                'path' => '6504573292dce.jpg',
                'alt' => 'Image sur le trick 360 Tail Grab',
                'trick_id' => 3,
            ],
            [
                'path' => 'defaut.jpg',
                'alt' => 'Image sur le trick 360 Tail Grab',
                'trick_id' => 3,
            ],
            [
                'path' => 'defaut.jpg',
                'alt' => 'Image sur le trick Indy Grab',
                'trick_id' => 2,
            ],
        ];
        

        $videos = [
            [
                'trick_id' => 1,
                'iframe' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/BDpxekjUCqw?si=9c9s-sQU6ksNdK8G" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
            ],
            [
                'trick_id' => 2,
                'iframe' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/G_MEz7oJzro?si=kgRq_54I7Dm7vUtp" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
            ],
            [
                'trick_id' => 3,
                'iframe' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/2o433Bqbldo?si=uRU1lm7u80H5FWsi&amp;start=12" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
            ],
        ];
        
        $discussions = [
            [
                'trick_id' => 1,
                'comment' => 'Super trick, j\'ai adoré le faire !',
                'created_at' => new \DateTimeImmutable('2023-09-15 16:00:00'),
                'author_id' => 1,
            ],
            [
                'trick_id' => 2,
                'comment' => 'L\'Indy Grab est un classique, mais il faut de la pratique !',
                'created_at' => new \DateTimeImmutable('2023-09-15 16:15:00'),
                'author_id' => 1,
            ],
            [
                'trick_id' => 3,
                'comment' => 'La rotation de 360 Tail Grab est un défi, mais tellement satisfaisante !',
                'created_at' => new \DateTimeImmutable('2023-09-15 16:30:00'),
                'author_id' => 1,
            ],
        ];

        $replies = [
            [
                'discussion_id' => 1,
                'author_id' => 1,
                'content' => 'Super discussion, j\'aime beaucoup ce sujet !',
            ],
            [
                'discussion_id' => 1,
                'author_id' => 1,
                'content' => 'Je suis d\'accord, c\'est vraiment intéressant.',
            ],
            [
                'discussion_id' => 2,
                'author_id' => 1,
                'content' => 'Merci pour cette réponse, c\'est très utile !',
            ],
            [
                'discussion_id' => 3,
                'author_id' => 1,
                'content' => 'Je n\'avais jamais pensé à cela, c\'est une excellente idée !',
            ],
        ];
        
        

    // Groupes
    $groups = [];
    foreach ($groups as $groupData) {
        $group = new Group();
        $group->setName($groupData['name']);
        $manager->persist($group);
        $groups[] = $group;
    }
    $manager->flush();

    // Users
    $users = [];
    foreach ($users as $userData) {
        $user = new User();
        $user->setEmail($userData['email']);
        $user->setPassword($userData['password']);
        $user->setRoles(json_decode($userData['roles']));
        $user->setToken($userData['token']);
        $user->setIsVerified($userData['is_verified']);
        $user->setUsername($userData['username']);
        $user->setAvatar($userData['avatar']);
        $manager->persist($user);
        $users[] = $user;
    }
    $manager->flush();

    // Tricks
    $tricks = [];
    foreach ($tricks as $trickData) {
        $trick = new Tricks();
        $trick->setIdGroup($groups[$trickData['group_id']]);
        $trick->setName($trickData['name']);
        $trick->setDescription($trickData['description']);
        $trick->setCreatedBy($users[$trickData['created_by_id']]);
        $trick->setCreatedAt(new \DateTimeImmutable($trickData['created_at']));
        $trick->setSlug($trickData['slug']);
        $manager->persist($trick);
        $tricks[] = $trick;
    }
    $manager->flush();

    // Images
    foreach ($images as $imageData) {
        $image = new Images();
        $image->setPath($imageData['path']);
        $image->setAlt($imageData['alt']);
        $image->setTrick($tricks[$imageData['trick_id']]);
        $manager->persist($image);
    }
    $manager->flush();

    // Videos
    foreach ($videos as $videoData) {
        $video = new Videos();
        $video->setTrick($tricks[$videoData['trick_id']]);
        $video->setIframe($videoData['iframe']);
        $manager->persist($video);
    }
    $manager->flush();

    // Discussions
    foreach ($discussions as $discussionData) {
        $discussion = new Discussions();
        $discussion->setIdTricks($tricks[$discussionData['trick_id']]);
        $discussion->setCreatedAt(new \DateTimeImmutable($discussionData['created_at']));
        $discussion->setContent($discussionData['content']);
        $discussion->setAuthor($users[$discussionData['author_id']]);
        $manager->persist($discussion);
    }
    $manager->flush();

    // Replies
    foreach ($replies as $replyData) {
        $reply = new Reply();
        $reply->getDiscussion($replyData['discussion_id']);
        $reply->setAuthor($users[$replyData['author_id']]);
        $reply->setContent($replyData['content']);
        $manager->persist($reply);
    }
    $manager->flush();

    }
}
