<?php


namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Create multiple products
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->word . ' Monitor');
            $product->setDescription($faker->sentence(10));
            $product->setPhoto($faker->imageUrl(640, 480, 'electronics'));
            $product->setPrice($faker->randomFloat(2, 50, 999.99));
            $product->setQuantity($faker->numberBetween(1, 100));

            $manager->persist($product);
        }

        $manager->flush();
    }
}

