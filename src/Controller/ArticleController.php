<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArticleController.php',
        ]);
    }

   /**
     
     * @Route("/articles", name="create_articles", methods={"POST"})
     */
   public function createAction(Request $request)
   {
       $article = new Article();
       $data = json_decode($request->getContent());
       
       $article->setTitle($data->title);
       $article->setContent($data->content);

       $em = $this->getDoctrine()->getManager();
       $em->persist($article);
       $em->flush();

       return new Response('', Response::HTTP_CREATED);
   }

    /**
     * @Route("/allArticlesNormalizer", name="listeNormalizer", methods={"GET"})
     * @param ArticleRepository $articleRepositoryn
     * @param NormalizerInterface $normalizerInterface
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
   public function getAllArticles(ArticleRepository $articleRepository, NormalizerInterface $normalizer ) :JsonResponse
   {
    // on récupère les éléments de la base de donnée
    $articles = $articleRepository->findAll();
    // on ne peut pas utliser dans ce cas la méthode jscon_encode car sinon on optient un tableau d'objet vide du fait que les attributs sont "private"
    // de ce fait j'implémente dans ma méthode l'interface Normalizer. il faut bien dans ce cas créer des groupes dans l'entity pour dire quels sont les attribut à faire appraitre
    //avec l'annotation @Groups("post:read")

       $articleNormalizer = $normalizer->normalize($articles,null,[]);
       $json = json_encode($articleNormalizer);
    return $response = new Response($json, 200,
    ['Content-Type' => 'application/json']
    );
   }

    /**
     * @Route("/allArticlesSerializer", name="listeSerialize", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @param SerializerInterface $serializer
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAllArticlesSerializer(ArticleRepository $articleRepository, SerializerInterface $serializer ):JsonResponse
    {
        // on récupère les éléments de la base de donnée
        $articles = $articleRepository->findAll();
        $json = $serializer->serialize($articles,'json', []);
        return $response = new JsonResponse($json, 200,true);
    }

    /**
     * @Route("/allArticlesSimple", name="listeSimple", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAllArticlesSimple(ArticleRepository $articleRepository ) :JsonResponse
    {
        // on récupère les éléments de la base de donnée
        $articles = $articleRepository->findAll();
        return $response = $this->json($articles, 200, [],[]);
    }
    //voir video https://www.youtube.com/watch?v=SG7GgcnR1F4&t=1501s
}
