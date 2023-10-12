<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Assets;

final class ToOneEntity
{
    protected string $id;
    protected NoRelationEntity $toOne;
    protected string $field;

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
     * @return NoRelationEntity
     */
    public function getToOne(): NoRelationEntity
    {
        return $this->toOne;
    }

    /**
     * @param NoRelationEntity $toOne
     */
    public function setToOne(NoRelationEntity $toOne): void
    {
        $this->toOne = $toOne;
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


}
