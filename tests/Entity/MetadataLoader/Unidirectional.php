<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Tests\Entity\MetadataLoader;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'unidirectional')]
final class Unidirectional
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    public string $id;
    #[ORM\OneToOne(targetEntity: Associated::class)]
    public Associated $oneToOne;

    #[ORM\ManyToMany(targetEntity: Associated::class)]
    public Associated $manyToMany;

    #[ORM\ManyToOne(targetEntity: Associated::class)]
    public Associated $manyToOne;
}