<?php

// Très important : indiquer le namespace du contrôleur pour qu'il soit retrouvé par Symfony
// Tous les contrôleurs (les fichiers se trouvant dans src/Controller) devront avoir ce namespace
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Le contrôleur doit hériter de AbstractController pour appeler la méthode render
class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(): Response
    {
        // Ce code n'est plus nécessaire : on appelle directement le template avec la méthode render
        // return new Response("<h1>Accueil</h1>");

        // Affichage du template qui s'appelle homepage.html.twig
        return $this->render('homepage.html.twig');
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }
}