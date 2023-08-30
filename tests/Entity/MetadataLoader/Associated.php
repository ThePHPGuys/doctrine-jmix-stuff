<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Tests\Entity\MetadataLoader;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'associated')]
final class Associated
{
    #[ORM\Id]
    #[ORM\Column(name: 'associatedPK', type: 'string')]
    public string $id;
}