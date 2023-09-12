<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\MetadataLoader;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'scalar_entity')]
final class ScalarEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(type: 'string')]
    public string $stringField;

    #[ORM\Column(type: 'string', nullable: true)]
    public string $stringFieldNullable;

    #[ORM\Column(type: 'datetime')]
    public string $dateTimeField;

}