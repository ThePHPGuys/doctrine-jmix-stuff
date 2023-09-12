<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\SameEntityAssociation;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Followers
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    public string $id;

    #[ORM\ManyToOne(User::class)]
    public User $from;

    #[ORM\ManyToOne(User::class)]
    public User $to;
}