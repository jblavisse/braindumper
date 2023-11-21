<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MemoryRepository;
use App\Entity\Memory;
use App\Form\AddMemoryType;
use App\Repository\UserRepository;
use App\Entity\Category;
use App\Entity\Type;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class MemoryController extends AbstractController
{


    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'app_memory')]
    #[IsGranted('ROLE_USER', message: "Vous devez être connecté(e) !")]
    public function show(MemoryRepository $memoryRepository, UserRepository $userRepository, TypeRepository $typeRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $memories = $memoryRepository
            ->findAll();
        $user = $this->getUser();

        $memory = new Memory();
        $memoryForm = $this->createForm(AddMemoryType::class, $memory);
        $memoryForm->handleRequest($request);


        if ($memoryForm->isSubmitted() && $memoryForm->isValid()) {
            $memory = $memoryForm->getData();
            $user = $this->getUser();
            $memory->setUser($user);
            $this->entityManager->persist($memory);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_memory');

        }


        return $this->render('memory/memory.html.twig', ['donnees' => $memories, 'user' => $user, 'memoryForm' => $memoryForm->createView()]);

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


    #[Route('/memories/{id}', name: 'memories_update', methods: ['put'])]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $memory = $entityManager->getRepository(Memory::class)->find($id);
        $newTitle = $request->request->get('title');


        if (!$memory) {
            return $this->json('No project found for id' . $id, 404);
        }

        $requestData = json_decode($request->getContent(), true);

        $newTitle = $requestData['title'] ?? null;
        $newDescription = $requestData['description'] ?? null;
        // $newType = $requestData['type'] ?? null;
        // $newCategory = $requestData['category'] ?? null;


        if ($newTitle !== null) {
            $memory->setTitle($newTitle);
        }

        if ($newDescription !== null) {
            $memory->setDescription($newDescription);
        }

        // if ($newType !== null) {
        //     $memory->setType($newType);
        // }
    
        // if ($newCategory !== null) {
        //     $memory->setCategory($newCategory); 
        // }
    

        $entityManager->persist($memory);
        $entityManager->flush();


        $data = [
            [
                'id' => $memory->getId(),
                'title' => $memory->getTitle(),
                'description' => $memory->getDescription(),
                // 'type'=> $memory->getType(),
                // 'category'=> $memory->getCategory(),
                'newTitle' => $newTitle,
                'newDescription' => $newDescription,
                // 'newCategory'=> $newCategory,
                // 'newType' => $newType,
                'request' => $request
            ]
        ];
        return $this->json($data);
    }
}