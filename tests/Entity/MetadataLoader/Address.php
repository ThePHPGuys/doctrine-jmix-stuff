<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\MetadataLoader;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Address
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    public string $id;

    #[ORM\Column(type: 'string')]
    public string $city;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    public Client $client;
}