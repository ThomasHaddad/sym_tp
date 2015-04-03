<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MovieController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $movieRepo=$this->getDoctrine()->getRepository("AppBundle:Movie");
        $movies=$movieRepo->findMovies(10);
        $params=[
            'movies'=>$movies,
            'index'=>10
        ];
        return $this->render('movies/movies.html.twig',$params);
    }

    /**
     * @Route("/movie/{id}", name="movieDetails")
     */
    public function movieDetailsAction($id)
    {
        $movieRepo=$this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie=$movieRepo->findOneById($id);
        if($movie){
            $params=[
                'movie'=>$movie,
            ];
        }
        return $this->render('movies/movie.html.twig',$params);
    }

    /**
     * @Route("/movie/viewed/{id}", name="movieViewed")
     */
    public function movieViewedAction($id)
    {
        $movieRepo=$this->getDoctrine()->getRepository("AppBundle:Movie");
        $movie=$movieRepo->findOneById($id);
        if($movie && $movie->getIsViewed()==0){
            $movie->setIsViewed(1);
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return new JsonResponse(true);
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/get-more-movies/{start}", name="getMoreMovies", defaults={"start":0})
     */
    public function getMoreMoviesAction($start)
    {
        $movieRepo=$this->getDoctrine()->getRepository("AppBundle:Movie");
        $movies=$movieRepo->findMoreMovies($start,$start+10);
        $params=[
            'movies'=>$movies
        ];
        return $this->render('movies/include-movies.html.twig',$params);
    }
}
