<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class IdEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    protected string $id;

    public function getId(): string
    {
        return $this->id;
    }

    protected function generateId(string $scope = null): void
    {
        $this->id = uniqid($scope);
    }
}