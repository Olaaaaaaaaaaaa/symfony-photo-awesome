<?php

namespace App\Controller\Admin;

use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact')]
class ContactController extends AbstractController
{

    public function __construct(
        private ContactRepository $contactRepository,
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {
    }

    #[Route('/', name: 'app_contactAdmin')]
    public function index(Request $request): Response
    {
        $qb = $this->contactRepository->getQbAll();

        $qb->andWhere('con.status LIKE :status')
            ->setParameter('status', "%" . "non_lu" . "%");


        $pagination = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 15);

        return $this->render('contactAdmin/index.html.twig', [
            'contacts' => $pagination
        ]);
    }

    #[Route('/show/{id}', name: 'app_contactAdmin_show')]
    public function detail($id): Response
    {
        $contactEntity = $this->contactRepository->find($id);

        if ($contactEntity === null) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('contactAdmin/show.html.twig', [
            'contact' => $contactEntity
        ]);
    }

    #[Route('/lu/{id}', name: 'app_contactAdmin_lu')]
    public function lu($id): Response
    {
        $contact = $this->contactRepository->find($id);
        $contact->setStatus("lu");
        if ($contact !== null) {



            $this->entityManager->persist($contact, true);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_contactAdmin');
    }
}
