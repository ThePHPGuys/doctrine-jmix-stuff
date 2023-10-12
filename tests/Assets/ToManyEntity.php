<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Assets;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class ToManyEntity
{
    protected string $id;
    protected string $field;
    protected Collection $entities;

    public function __construct()
    {
        $this->entities = new ArrayCollection();
    }


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function getEntities(): Collection
    {
        return $this->entities;
    }

    public function addEntities(Collection $entities)
    {
        foreach ($entities as $entity) {
            $this->entities->add($entity);
        }
    }

    public function removeEntities(Collection $entities)
    {
        foreach ($entities as $entity) {
            $this->entities->removeElement($entity);
        }
    }
}
