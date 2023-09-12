<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\MetadataLoader;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TPG\PMix\MetaModel\Attribute\Composition;

#[ORM\Entity]
final class Order
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string')]
    public string $id;

    /**
     * @var Collection<OrderLine>
     */
    #[Composition]
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class)]
    public Collection $lines;

    #[ORM\Column(type: 'string', enumType: OrderStatus::class)]
    public OrderStatus $status;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    public Client $client;

    public function __construct()
    {
        $this->id = uniqid('order');
        $this->lines = new ArrayCollection();
    }


}