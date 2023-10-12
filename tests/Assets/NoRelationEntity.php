<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Assets;

final class NoRelationEntity
{
    protected $id;

    protected $field;
    private bool $loaded = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field): void
    {
        $this->field = $field;
    }

    public function setLoaded(bool $loaded): void
    {
        $this->loaded = $loaded;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }


}
