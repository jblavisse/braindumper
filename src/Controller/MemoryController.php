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

    #[Route('/', name: 'app_memory')]
    public function show(MemoryRepository $memoryRepository): Response
    {
        $memory = $memoryRepository
            ->findAll();

        return $this->render('memory/memory.html.twig', ['donnees' => $memory]);

    }

    private $entityManager;
    private $taskRepository;
    #[Route('/form', name: 'add_memory')]
    public function __construct(EntityManagerInterface $entityManager, MemoryRepository $memoryRepository)
    {
        $this->entityManager = $entityManager;
        $this->memoryRepository = $memoryRepository;
    }

    public function addMemory(Request $request): Response
    {
        $memory = new Memory();
        $form = $this->createForm(AddMemoryType::class, $memory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($memory);
            $this->entityManager->flush();

            return $this->redirectToRoute('memory_list');
        }

        return $this->render('memory/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}