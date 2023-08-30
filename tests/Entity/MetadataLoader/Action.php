<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Tests\Entity\MetadataLoader;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Action
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    public string $id;

    #[ORM\Column(type: 'string')]
    public string $action;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    public Client $client;
}