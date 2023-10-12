<?php
declare(strict_types=1);

namespace TPG\PMix\Security;

use TPG\PMix\MetaModel\MetaClass;

final class ExportImportEntityContext implements AccessContext
{
    private array $disabledToImport = [];
    private array $disabledToExport = [];

    public function __construct(MetaClass $metaClass)
    {

    }

    public function canExport(string $attribute): bool
    {
        return !in_array($attribute, $this->disabledToExport);
    }

    public function canImport(string $attribute): bool
    {
        return !in_array($attribute, $this->disabledToImport);
    }

    public function disableImport(string $attribute): void
    {
        $this->disabledToImport[] = $attribute;
    }

    public function disableExport(string $attribute): void
    {
        $this->disabledToExport[] = $attribute;
    }
}
