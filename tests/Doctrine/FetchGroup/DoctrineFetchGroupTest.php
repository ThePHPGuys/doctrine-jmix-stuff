<?php
declare(strict_types=1);

namespace TPG\PMix\Tests\Doctrine\FetchGroup;


use Doctrine\ORM\EntityManagerInterface;
use TPG\PMix\Declined\View;
use TPG\PMix\Tests\DoctrineTestCase;

final class DoctrineFetchGroupTest extends DoctrineTestCase
{
    private function entityManager(): EntityManagerInterface
    {
        return $this->getEntityManager([__DIR__ . '/../../Entity/MetadataLoader']);
    }

    public function testFetchGroup()
    {
        $clientFetchGroup = new View();
        $clientFetchGroup->addAttribute('name');

        $group = new View();
        $group->addAttribute('createdAt');
        $group->addAttribute('amount');
        $group->addAttribute('client', $clientFetchGroup);

        self::assertEquals('client', $group->getAttribute('client')->getPath());
        self::assertEquals('client', $group->getAttribute('client')->getView()->getPath());
        self::assertEquals('client.name', $group->getAttribute('client')->getView()->getAttribute('name')->getPath());
    }

    public function testCreateNested()
    {
        $group = new View();
        $group->addAttribute('client.name');
        $group->addAttribute('client.name.id');
        self::assertEquals('client', $group->getAttribute('client')->getPath());
        self::assertEquals('client', $group->getAttribute('client')->getView()->getPath());
        self::assertEquals('client.name', $group->getAttribute('client')->getView()->getAttribute('name')->getPath());
        self::assertEquals('client.name.id', $group->getAttribute('client')->getView()->getAttribute('name')->getView()->getAttribute('id')->getPath());
    }

    public function testGetNested()
    {
        $group = new View();
        $group->addAttribute('client.name');
        $group->addAttribute('client.name.id');
        self::assertEquals('client.name.id', $group->getAttribute('client.name.id')->getPath());
    }

    public function testClone()
    {
        $group = new View();
        $group->addAttribute('client.name');
        $group->addAttribute('client.name.id');
        $clientGroup = $group->getAttribute('client')->getView()->clone();
        self::assertEquals('', $clientGroup->getPath());
        self::assertEquals('name.id', $clientGroup->getAttribute('name.id')->getPath());
        self::assertEquals('name', $clientGroup->getAttribute('name')->getPath());
    }


}