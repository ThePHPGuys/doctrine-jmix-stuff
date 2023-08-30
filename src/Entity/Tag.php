<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Entity;


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Tag extends IdEntity
{
    #[ORM\Column(type: 'string')]
    public string $name;

    public function __construct()
    {
        $this->generateId('tag');
    }

    public function getName(): string
    {
        return $this->name;
    }
}