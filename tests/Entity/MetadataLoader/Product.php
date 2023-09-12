<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\MetadataLoader;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    public string $id;

    #[ORM\Column(type: 'string')]
    public string $name;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    public Collection $tags;
}