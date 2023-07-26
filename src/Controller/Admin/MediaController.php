<?php

namespace App\Controller\Admin;

use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/media')]
class MediaController extends AbstractController
{

    public function __construct(
        private MediaRepository $mediaRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_media')]
    public function index(): Response
    {
        $mediaEntities = $this->mediaRepository->findAll();

        return $this->render('media/index.html.twig', [
            'medias' => $mediaEntities
        ]);
    }

    #[Route('/show/{id}', name: 'app_media_show')]
    public function detail($id): Response
    {

        $mediaEntity = $this->mediaRepository->find($id);

        if ($mediaEntity === null) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('media/show.html.twig', [
            'media' => $mediaEntity
        ]);
    }
}
