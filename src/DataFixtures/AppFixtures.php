<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $arr = [];
        // create 10 Categories!
        for ($i = 1; $i <= 10; $i++) {
            $category = new Category();
            $category->setName($faker->country);
            $category->setCreated(new \DateTime());

            $manager->persist($category);
            $arr[] = $category;
        }

        $manager->flush();

        // create 20 Articles!
        for ($i = 1; $i <= 20; $i++) {
            $article = new Article();
            $article->setTitle($faker->city);
            $article->setContent($faker->text);

            $isPublished = mt_rand(0, 2);
            if ($isPublished == 2) {
                $article->setStatus(2);
                $article->setPublished(new \DateTime());
            } else {
                $article->setStatus(mt_rand(0, 1));
            }

            $article->setCategory($arr[mt_rand(0, 9)]);

            $article->setTrending(mt_rand(0, 1));
            $article->setCreated(new \DateTime());

            $manager->persist($article);
        }

        $manager->flush();

    }
}
