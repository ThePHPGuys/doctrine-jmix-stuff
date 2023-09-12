<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\MetadataLoader;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Client
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    public string $id;

    #[ORM\Column(type: 'string')]
    public string $name;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Address::class)]
    public Collection $addresses;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Action::class)]
    public Collection $actions;
}