<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

final readonly class ViewProperty
{
    /**
     * @param string $name
     * @param View|null $view - View of the property if the corresponding MetaClass attribute is association
     */
    public function __construct(public string $name, public ?View $view = null)
    {

    }

}