<?php


namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('UltraHD Monitor');
        $product->setDescription('A 32-inch UltraHD monitor, perfect for both gaming and professional use.');
        $product->setPhoto('https://example.com/ultrahd-monitor.jpg');
        $product->setPrice('110.99');

        $manager->persist($product);

        // You can create more products here
        $manager->flush();
    }
}
