<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');
        for($i = 0 ; $i < 20; $i++){
            $article = new Article();
            $article
                ->setTitle($faker->words(4,true))
                ->setContent($faker->sentences(10,true))
                ->setStatus(true);

            $manager->persist($article);
        }
        $manager->flush();
    }
}
