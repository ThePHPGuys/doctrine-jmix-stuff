<?php
declare(strict_types=1);

namespace TPG\PMix;

use TPG\PMix\Data\DataManager as DataManagerInterface;
use TPG\PMix\Data\DataStores;
use TPG\PMix\Data\UnconstrainedDataManager;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\Security\AccessConstraintsRegistry;

final readonly class DataManager extends UnconstrainedDataManagerImpl implements DataManagerInterface
{

    public function __construct(
        DataStores                           $dataStores,
        MetaData                             $metaData,
        private UnconstrainedDataManagerImpl $unconstrainedDataManager,
        private AccessConstraintsRegistry    $constraintsRegistry
    )
    {
        parent::__construct($dataStores, $metaData);
    }

    protected function getAppliedConstraints(): array
    {
        return $this->constraintsRegistry->getConstraints();
    }

    public function unconstrained(): UnconstrainedDataManager
    {
        return $this->unconstrainedDataManager;
    }


}
