<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findLatestPosts();

        return $this->render('admin/index.html.twig', [
            'posts' => $posts
        ]);
    }
}