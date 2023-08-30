<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table("orders")]
class Order extends IdEntity
{
    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'decimal')]
    public float $amount = 0;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    public Client $client;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    public Collection $tags;

    public function __construct()
    {
        $this->generateId('order');
        $this->createdAt = new \DateTimeImmutable();
        $this->tags = new ArrayCollection();
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}