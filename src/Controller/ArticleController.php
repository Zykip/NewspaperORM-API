<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends ApiController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->transformAll();

        return $this->respond($articles);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {

        $data = json_decode(
            $request->getContent(),
            true
        );


        $article = new Article();
        $article->setCreated(new \DateTime());
        $cat = $em->getRepository(Category::class)->find($data['category']);
        $article->setCategory($cat);

        $form = $this->createForm(ArticleType::class, $article);

        $form->submit($data);

        if (false === $form->isValid()) {
          /*  $errors = [];

            if (count($form->getErrors()) > 0) {
                foreach ($form->getErrors() as $error) {
                    $errors[] = $error->getMessage();
                }
            } else {
                foreach ($form->all() as $child) {
                    $childTree = self::getFormErrorsTree($child);

                    if (count($childTree) > 0) {
                        $errors[$child->getName()] = $childTree;
                    }
                }
            }

            dd($errors);*/

            return $this->respondWithErrors('Provide all valid values');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->respond(['success']);
    }

    /**
     * @Route("/trending", name="trending_articles", methods={"GET"})
     */
    public function trending(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->getTrendingArticles();

        return $this->respond($articles);
    }

    /**
     * @Route("/category/{id}", name="category_articles", methods={"GET"})
     */
    public function categoryArticles($id, ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->getCategoryArticles($id);

        if(!$articles){
            return $this->respondWithErrors('No articles found with this category id '. $id);
        }

        return $this->respond($articles);
    }

    /**
     * @Route("/publish/{id}", name="publish_articles", methods={"PUT"})
     */
    public function publishArticles($id,ArticleRepository $articleRepository, EntityManagerInterface $em)
    {
        $article = $articleRepository->find($id);

        if(!$article){
            return $this->respondWithErrors('No articles found with this id '. $id);
        }

        $article->setStatus(2);
        $article->setPublished(new \DateTime());

        $em->flush();

        return $this->respond('Articles published successfully!');
    }
}
