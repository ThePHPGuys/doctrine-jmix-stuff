<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Misterx\DoctrineJmix\Entity\Client;
use Misterx\DoctrineJmix\Entity\Order;
use Misterx\DoctrineJmix\Entity\Tag;

final class OrderClientsFixture implements FixtureInterface
{
    private ObjectManager $manager;

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $this->manager = $manager;
        $clients = $this->createMany(Client::class, 10, function (Client $client) use ($faker) {
            $client->name = $faker->name;
        });

        $tags = $this->createMany(Tag::class, 10, function (Tag $tag) use ($faker) {
            $tag->name = $faker->word;
        });

        array_map(
            function (Client $client) use ($faker, $tags) {
                $this->createMany(Order::class, 10,
                    function (Order $order) use ($client, $tags, $faker) {
                        $order->client = $client;
                        $order->amount = $faker->randomFloat(2, 1, 1000);
                        array_map(fn(int $tagIndex) => $order->tags->add($tags[$tagIndex]), array_rand($tags, 5));
                    });
            }, $clients);
        $manager->flush();
    }

    protected function createMany(string $className, int $count, callable $factory)
    {
        $entities = [];
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);
            $entities[] = $entity;
            $this->manager->persist($entity);
        }

        return $entities;
    }

}