<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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


       //$article = $this->container->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Article', 'json');

       $em = $this->getDoctrine()->getManager();
       $em->persist($article);
       $em->flush();

       return new Response('', Response::HTTP_CREATED);
   }

   /**
 * @Route("/allArticles", name="liste", methods={"GET"})
 */
   public function getAllArticles(ArticleRepository $articleRepository){

    // on récupère les éléments de la base de donnée
    $articles = $articleRepository->findAll();

    // on spécifie qu'on utilise en encodeur en json 
    $encoders = [new JsonEncoder()];

    //on instancie le "normaliser" pour converti la collection  en tableau
    $normalizers = [new ObjectNormalizer()];

    // on fait la conversion json
    // on instancie le convertisseur
    $serializer = new Serializer($normalizers,$encoders);

    //on convertie en Json
    $jsonContent = $serializer->serialize($articles, 'json',[
        'circular_reference_handler' => function($object){
            return $object->getId();
        }
    ]);

    //on instancie la réponse
    $response = new Response($jsonContent);

    // on ajoute l'entete HTTP
    $response->headers->set('Content-type','application/json');
    
    // on envoie la réponse
    return $response;
    
   }

    
}
