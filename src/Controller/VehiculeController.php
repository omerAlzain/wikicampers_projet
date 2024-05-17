<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Entity\Reservation;
use App\Form\VehiculeType;
use App\Form\ReservationType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\Collections\ArrayCollection;


#[Route('/vehicule')]
class VehiculeController extends AbstractController
{
    #[Route('/', name: 'vehicule_index', methods: ['GET'])]
    public function index(Request $request, VehiculeRepository $vehiculeRepository): Response
    {
        $search = $request->query->get('search');
        $price = $request->query->get('price');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');

        $queryBuilder = $vehiculeRepository->createQueryBuilder('v');

        if ($search) {
            $queryBuilder->andWhere('v.marque LIKE :search OR v.modele LIKE :search')
                         ->setParameter('search', '%'.$search.'%');
        }

        if ($price) {
            $queryBuilder->join('v.disponibilites', 'd')
                         ->andWhere('d.prixParJour <= :price')
                         ->setParameter('price', $price);
        }

        if ($dateDebut && $dateFin) {
            $queryBuilder->join('v.disponibilites', 'd')
                         ->andWhere('d.dateDebut <= :dateFin AND d.dateFin >= :dateDebut')
                         ->setParameter('dateDebut', $dateDebut)
                         ->setParameter('dateFin', $dateFin);
        }

        $vehicules = $queryBuilder->getQuery()->getResult();

        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }

    #[Route('/new', name: 'vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/images/vehicules',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                $vehicule->setImageName($newFilename);
            }

            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('vehicule_index');
        }

        return $this->render('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    
    
    #[Route('/{id}', name: 'vehicule_show', methods: ['GET', 'POST'])]
    public function show(Vehicule $vehicule, Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        $now = new \DateTime();
        $isAvailable = true;

        foreach ($vehicule->getReservations() as $res) {
            if ($res->getDateDebut() <= $now && $res->getDateFin() >= $now) {
                $isAvailable = false;
                break;
            }
        }

        if ($isAvailable && $form->isSubmitted() && $form->isValid()) {
            $reservation->setVehicule($vehicule);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('vehicule_show', ['id' => $vehicule->getId()]);
        }

        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
            'isAvailable' => $isAvailable,
        ]);
    }





    #[Route('/{id}/edit', name: 'vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/images/vehicules',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                $vehicule->setImageName($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('vehicule_index');
        }

        return $this->render('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vehicule_index');
    }
}


