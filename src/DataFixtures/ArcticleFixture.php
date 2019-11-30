<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;


class ArcticleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $userRepository = $manager->getRepository(User::class);
        $categoryRepository = $manager->getRepository(Category::class);
        $faker = Factory::create('fr_FR');

        for($i = 0 ; $i < 20; $i++){
            $article = new Article();
            $article
                ->setTitle($faker->words(4, true))
                ->setContent($faker->sentences(10, true))
                ->setStatus(true)
                ->setCreatedAt(new \DateTime())
                ->setCategoryId($categoryRepository->find(random_int(1,4)))
                ->setUserId($userRepository->find(random_int(29,30)));
            $manager->persist($article);
        }
        $manager->flush();
    }
}


