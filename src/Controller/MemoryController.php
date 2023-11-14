<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MemoryRepository;
use App\Entity\Memory;
use App\Form\AddMemoryType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;



class MemoryController extends AbstractController
{



    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    #[Route('/', name: 'app_memory')]
    public function show(MemoryRepository $memoryRepository, Request $request): Response
    {
        $memories = $memoryRepository
            ->findAll();

        $memory = new Memory();
        $memoryForm = $this->createForm(AddMemoryType::class, $memory);
        $memoryForm->handleRequest($request);
        

        if ($memoryForm->isSubmitted() && $memoryForm->isValid()) {
            $this->entityManager->persist($memory);
            $this->entityManager->flush();

        }
        return $this->render('memory/memory.html.twig', ['donnees' => $memories, 'memoryForm' => $memoryForm->createView()]);
    }
}