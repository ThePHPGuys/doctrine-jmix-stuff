<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\MetadataLoader;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tag')]
final class Tag
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    public string $id;

    #[ORM\Column(type: 'string')]
    public string $name;
}