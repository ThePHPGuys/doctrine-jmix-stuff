<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Tests\Entity\SameEntityAssociation;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    public string $id;

    #[ORM\OneToMany(mappedBy: 'to', targetEntity: Followers::class)]
    public Collection $followers;

    #[ORM\OneToMany(mappedBy: 'from', targetEntity: Followers::class)]
    public Collection $following;
}