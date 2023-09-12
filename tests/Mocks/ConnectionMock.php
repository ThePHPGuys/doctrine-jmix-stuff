<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Mocks;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use SensitiveParameter;

final class ConnectionMock implements Driver
{
    public function connect(#[SensitiveParameter] array $params)
    {
        // TODO: Implement connect() method.
    }

    public function getDatabasePlatform()
    {
        return new SqlitePlatform();
    }

    public function getSchemaManager(\Doctrine\DBAL\Connection $conn, AbstractPlatform $platform)
    {
        // TODO: Implement getSchemaManager() method.
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        // TODO: Implement getExceptionConverter() method.
    }


}