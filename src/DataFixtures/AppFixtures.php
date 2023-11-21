<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    /**
     * Undocumented function
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        // Ingredient
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($faker->name);
            $ingredient->setPrice($faker->randomFloat(2, 0, 200));
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recipe
        for ($j = 0; $j < 25; $j++) {
            $recipe = new Recipe();
            $recipe->setName($faker->name)
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1400) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($faker->text)
                ->setPrice($faker->randomFloat(2, 1, 1000))
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);

            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $manager->persist($recipe);
        }

        // User
        for ($k = 0; $k < 10; $k++) {
            $user = new User();
            $user->setFullname($faker->name)
                ->setPseudo(mt_rand(0, 1) === 1 ? $faker->firstName : null)
                ->setEmail($faker->email)
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');

            $manager->persist($user);
        }

        $manager->flush();
    }
}
