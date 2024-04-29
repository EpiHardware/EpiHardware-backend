<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Faker\Factory;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Récupération des utilisateurs existants
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            // Créer entre 1 et 5 commandes par utilisateur
            for ($j = 0; $j < mt_rand(1, 5); $j++) {
                $order = new Order();
                $order->setUser($user);
                $order->setTotalPrice($faker->randomFloat(2, 20, 1000)); // Prix entre 20 et 1000
                $order->setCreationDate($faker->dateTimeThisYear);

                // Ajouter des produits à chaque commande
                for ($k = 0; $k < mt_rand(1, 10); $k++) {
                    $product = new Product();
                    $product->setName($faker->word);
                    $product->setDescription($faker->sentence);
                    $product->setPhoto($faker->imageUrl(640, 480, 'technics'));
                    $product->setPrice($faker->randomFloat(2, 1, 200));
                    $product->setQuantity($faker->numberBetween(1, 100));
                    $product->setOrder($order);
                    $manager->persist($product);
                }

                $manager->persist($order);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
