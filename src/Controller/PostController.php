<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    public function create(PostRepository $repository, Request $request, ?int $id = null): Response
    {
        // Afficher un formulaire ou enregistrer l'article en base de données (si le formulaire a été soumis)
        if (! $id) {
            // Création d'un objet vide
            $post = new Post();
        } else {
            $post = $repository->find($id);

            if (! $post) {
                throw $this->createNotFoundException("L'article n'existe pas");
            }
        }

        // Création d'un formulaire pour mettre à jour la variable $post
        $form = $this->createForm(PostType::class, $post);

        // Gestion de la requête
        $form->handleRequest($request);

        // Si le formulaire est soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement de l'article en base de données
            $repository->save($post, true);

            // Retour à la liste de tous les articles
            return $this->redirectToRoute('posts_index');
        }

        // Affichage du formulaire
        return $this->render('post/create.html.twig', [
            'form' => $form,
            'id' => $id
        ]);
    }

    public function index(PostRepository $repository): Response
    {
        // Récupère tous les articles dans la base de données
        $posts = $repository->findLatestPosts();

        // Affichage de la vue
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    public function show(int $id, PostRepository $repository): Response
    {
        // Récupère l'article correspondant à l'id qui se trouve dans l'url
        $post = $repository->find($id);

        // Si l'article est null ça signifique qu'il n'existe pas => envoie d'une page 404
        if ($post === null) {
            throw $this->createNotFoundException("L'article $id n'existe pas");
        }

        // Affichage de la vue
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}