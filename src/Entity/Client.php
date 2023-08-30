<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Misterx\DoctrineJmix\MetaModel\Attribute\Property;


#[ORM\Entity]
class Client extends IdEntity
{
    #[ORM\Column(type: "string")]
    public string $name;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Order::class)]
    public Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->generateId('client');
    }

    #[Property]
    public function getFullName(): string
    {

    }

}