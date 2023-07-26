<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaSearchType;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/media')]
class MediaController extends AbstractController
{

    public function __construct(
        private MediaRepository $mediaRepository,
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {
    }

    #[Route('/', name: 'app_media')]
    public function index(Request $request): Response
    {

        $qb = $this->mediaRepository->getQbAll();

        $form = $this->createForm(MediaSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['mediaTitle'] !== null) {
                $qb->andWhere('m.title LIKE :title')
                    ->setParameter('title', "%" . $data['mediaTitle'] . "%");
            }
            if ($data['userEmail'] !== null) {
                $qb->innerJoin('m.user', 'u')
                    ->andWhere('u.email = :email')
                    ->setParameter('email', $data['userEmail']);
            }
            if ($data['mediaCreatedAt'] !== null) {
                $qb->andWhere('m.createdAt > :createdAt')
                    ->setParameter('createdAt', $data['mediaCreatedAt']);
            }
        }

        $pagination = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 15);

        // $mediaEntities = $this->mediaRepository->findAll();

        return $this->render('media/index.html.twig', [
            'medias' => $pagination,
            'form' => $form->createView()
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
