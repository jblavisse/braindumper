<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MemoryRepository;
use App\Entity\Memory;
use App\Form\AddMemoryType;
use App\Form\AddMemoryDescriptionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


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
            $memory = $memoryForm->getData();

            $this->entityManager->persist($memory);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_memory');

        }


        return $this->render('memory/memory.html.twig', ['donnees' => $memories, 'memoryForm' => $memoryForm->createView()]);

    }

    #[Route('/memories/{id}', name: 'memories_delete', methods: ['delete'])]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $memory = $entityManager->getRepository(Memory::class)->find($id);

        if (!$memory) {
            return $this->json(['error' => 'Le Memory avec l\'ID ' . $id . ' n\'existe pas.']);
        }

        $entityManager->remove($memory);
        $entityManager->flush();

        return $this->json(['success' => 'Le Memory avec l\'ID ' . $id . ' est supprimé.']);
    }


    #[Route('/memories/{id}', name: 'memories_update', methods: ['put', 'patch'])]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $memory = $entityManager->getRepository(Memory::class)->find($id);
        $newTitle = $request->request->get('title');

        if (!$memory) {
            return $this->json('No project found for id' . $id, 404);
        }

        if ($newTitle !== null) {
            $memory->setTitle($newTitle);
            $memory->setDescription($request->request->get('description'));
            $entityManager->flush();
        }


        $data = [
            'id' => $memory->getId(),
            'title' => $memory->getTitle(),
            'description' => $memory->getDescription(),
        ];

        return $this->json($data);
    }
}