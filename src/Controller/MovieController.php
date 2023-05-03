<?php

namespace App\Controller;

use App\Model\MovieModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController
{
    public function index(): Response
    {
        $model = new MovieModel();
        $movies = $model->findAll();

        return $this->render('movie/index.html.twig', [
            'movies' => $movies
        ]);
    }

    public function show(int $id): Response
    {
        $model = new MovieModel();
        $movie = $model->find($id);
        $actors = $model->findActors($id);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'actors' => $actors
        ]);
    }
}