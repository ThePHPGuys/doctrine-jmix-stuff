<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Security;

use Misterx\DoctrineJmix\MetaModel\MetaClass;

final class CRUDEntityContext implements AccessContext
{

    private bool $canCreate = true;
    private bool $canRead = true;
    private bool $canUpdate = true;
    private bool $canDelete = true;

    public function __construct(private MetaClass $metaClass)
    {

    }

    public function getMetaClass(): MetaClass
    {
        return $this->metaClass;
    }

    /**
     * @return bool
     */
    public function canCreate(): bool
    {
        return $this->canCreate;
    }

    /**
     * @param bool $canCreate
     */
    public function setCanCreate(bool $canCreate): void
    {
        $this->canCreate = $canCreate;
    }

    /**
     * @return bool
     */
    public function canUpdate(): bool
    {
        return $this->canUpdate;
    }

    /**
     * @param bool $canUpdate
     */
    public function setCanUpdate(bool $canUpdate): void
    {
        $this->canUpdate = $canUpdate;
    }

    public function canDelete(): bool
    {
        return $this->canDelete;
    }

    public function setCanDelete(bool $canDelete): void
    {
        $this->canDelete = $canDelete;
    }

    public function isCanRead(): bool
    {
        return $this->canRead;
    }

    public function setCanRead(bool $canRead): void
    {
        $this->canRead = $canRead;
    }

}