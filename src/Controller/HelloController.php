<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HelloController extends AbstractController
{
    public function index(string $name): Response
    {
        // Cette variable n'existe QUE dans la méthode index
//        $name = 'Toto';

        $days = [
            'Lundi',
            'Mardi',
            'Mercredi',
            'Jeudi',
            'Vendredi',
            'Samedi',
            'Dimanche'
        ];

        $user = [
            'email' => 'john.doe@test.fr',
            'fullname' => 'John Doe'
        ];

        // Pour passer le contenu de la variable à la vue (au template)
        // il faut l'indiquer en 2ème paramètre dans un tableau
        return $this->render( view: 'hello.html.twig', parameters: [
            'name' => $name,
            'age' => 17,
            'days' => $days,
            'user' => $user,
            'product' => [
                'price' => 12,
                'buyDate' => new \DateTime('2023-03-15')
            ]
        ]);
    }
}