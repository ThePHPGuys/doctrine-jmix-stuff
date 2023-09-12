<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Entity\MetadataLoader;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class OrderLine
{
    #[ORM\Id]
    #[ORM\Column(name: 'orderLinePk', type: 'string')]
    public string $id;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    public Product $product;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
    public Order $order;

    public function __construct()
    {
        $this->id = uniqid('orderLine');
    }
}