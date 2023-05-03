<?php

namespace App\Controller;

use App\Model\ActorModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ActorController extends AbstractController
{
    public function index(): Response
    {
       $model = new ActorModel();
       $actors = $model->findAll();

        return $this->render('actor/index.html.twig', [
            'actors' => $actors
        ]);
    }

    // $id est le numéro de l'acteur que l'on retrouve dans l'url
    public function show(int $id): Response
    {
        $model = new ActorModel();
        $actor = $model->find($id);
        $movies = $model->findMovies($id);

        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
            'movies' => $movies
        ]);
    }
}