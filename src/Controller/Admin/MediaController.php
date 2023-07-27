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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/media')]
class MediaController extends AbstractController
{

    public function __construct(
        private MediaRepository $mediaRepository,
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator,
        private ParameterBagInterface $parameterBag
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

    #[Route('/add', name: 'app_media_add')]
    public function add(Request $request, SluggerInterface $slugger): Response
    {
        /**
         * Récupérer le user connecté
         * Soit une entité User (si connecté)
         * Soit null (si pas connecté)
         */
        $user = $this->getUser();

        $uploadDirectory = $this->getParameter('upload_file');

        $media = new Media();
        //Je relis le media au user connecté
        $media->setUser($user);
        //Je donne la date du jour
        $media->setCreatedAt(new \DateTime());

        $form = $this->createForm(MediaType::class, $media);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $title = $media->getTitle();
            $slug = $slugger->slug($title);
            $media->setSlug($slug);

            $file = $form->get('file')->getData();
            if ($file) {
                /** @var Uploaded $file */
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

                $safeFilename = $slugger->slug($originalFileName);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                //Je bouge la photo dans le dossier d'upload avec un nouveau nom
                try {
                    $file->move(
                        $this->getParameter('upload_file'),
                        $newFilename
                    );
                    $media->setFilePath($newFilename);
                } catch (FileException $e) {
                }
            }

            $this->entityManager->persist($media);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_media');
        }

        return $this->render('media/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
