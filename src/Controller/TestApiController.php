<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\MediaRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController extends AbstractController
{

    public function __construct(
        private CategoryRepository $categoryRepository,
        private MediaRepository $mediaRepository,
        private PaginatorInterface $paginator
    ) {
    }

    #[Route('/api/test_connection', name: 'app_test_api')]
    public function index(): Response
    {
        /**@var User $user  */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json('non connecté');
        } else {


            return $this->json('connecté en tant que ' . $user->getEmail());
        }
    }

    #[Route('/api/category', name: 'api_category')]
    public function apiCategoryCollection(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->json($categories, 200, [], ['groups' => 'simple']);
    }

    #[Route('/api/media', name: 'api_media')]
    public function apiMediaCollection(Request $request): Response
    {
        $medias = $this->mediaRepository->getQbAll();

        $pagination = $this->paginator->paginate($medias, $request->query->getInt('page', 1), 15);

        return $this->json($pagination, 200, [], ['groups' => 'media_simple']);
    }
}
