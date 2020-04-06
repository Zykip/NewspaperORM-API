<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function transform(Article $article)
    {
        return [
            'id'    => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'status' => $article->getStatus(),
            'trending' => $article->getTrending(),
            'created' => $article->getCreated(),
            'published' => $article->getPublished(),
            'category_id' => $article->getCategory()->getId(),
            'category_name' => $article->getCategory()->getName(),
        ];
    }

    public function transformAll()
    {
        $articles = $this->findAll();

        return $this->transformData($articles);
    }

    private function transformData($data){
        $articlesArray = [];

        foreach ($data as $article) {
            $articlesArray[] = $this->transform($article);
        }

        return $articlesArray;
    }

    public function getTrendingArticles(){
        $d = $this->createQueryBuilder('a')
            ->where('a.trending = 1')
            ->getQuery()->getResult();

        return $this->transformData($d);
    }

    public function getCategoryArticles($catId){
        $d = $this->createQueryBuilder('a')
            ->where('a.category = :cat')
            ->setParameter('cat', $catId)
            ->getQuery()->getResult();

        return $this->transformData($d);
    }
}
