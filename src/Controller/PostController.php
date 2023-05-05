<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    public function create(PostRepository $repository, Request $request, ?int $id = null): Response
    {
        // Si l'utilisateur est authentifié, il a accès à cette page
        // Sinon il est redirigé vers la page de connexion
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Afficher un formulaire ou enregistrer l'article en base de données (si le formulaire a été soumis)
        if (! $id) {
            // Création d'un objet vide
            $post = new Post();
        } else {
            $post = $repository->find($id);

            if (! $post) {
                throw $this->createNotFoundException("L'article n'existe pas");
            }

            // Si l'utilisateur connecté n'est pas l'auteur de l'article
            if ($this->getUser() !== $post->getUser()) {
                // L'accès est refusé sauf si on est admin
                $this->denyAccessUnlessGranted('ROLE_ADMIN');
            }
        }

        // Création d'un formulaire pour mettre à jour la variable $post
        $form = $this->createForm(PostType::class, $post);

        // Gestion de la requête
        $form->handleRequest($request);

        // Si le formulaire est soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'utilisateur connecté
            $user = $this->getUser();

            // On indique que l'article a comme auteur l'utilisateur connecté
            $post->setUser($user);

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

    public function show(int $id, PostRepository $repository, Request $request, CommentRepository $commentRepository): Response
    {
        // Récupère l'article correspondant à l'id qui se trouve dans l'url
        $post = $repository->find($id);

        // Si l'article est null ça signifique qu'il n'existe pas => envoie d'une page 404
        if ($post === null) {
            throw $this->createNotFoundException("L'article $id n'existe pas");
        }

        // Gestion des commentaires
        // Récupération de tous les commentaires de l'article

        // 1ère façon de faire : avec le CommentRepository
        $comments = $commentRepository->findBy(['post' => $post]);

        // 2ème façon de faire : avec la méthode getComments()
        $comments = $post->getComments();

        // Création d'un commentaire vide
        $comment = new Comment();

        // On précise quel article est rattaché au commentaire
        // Lors de l'insertion du commentaire, il utilisera l'id du post rattaché
        $comment->setPost($post);

        $form = $this->createForm(CommentType::class, $comment);

        // Soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();
            $comment->setUser($user);

            // Enregistrement du commentaire
            $commentRepository->save($comment, true);

            // Redirection vers la page de l'article
            return $this->redirectToRoute('posts_show', ['id' => $post->getId()]);
        }

        // Affichage de la vue
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form,
            'comments' => $comments
        ]);
    }

    public function delete(EntityManagerInterface $manager, int $id): Response
    {
        // Si l'utilisateur n'est pas admin on refuse l'accès à cette action
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupération de l'article correspondant à l'id
        $post = $manager->getReference(Post::class, $id);

        // Suppression de l'article dans la base de données
        $manager->remove($post);
        $manager->flush();

        // Redirection vers la page d'administration
        return $this->redirectToRoute('admin');
    }
}