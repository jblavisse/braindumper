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
use Symfony\Component\Security\Core\Security;


class MemoryController extends AbstractController
{


    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'app_memory')]
    #[IsGranted('ROLE_USER', message: "Vous devez être connecté(e) !")]
    public function show(
        MemoryRepository $memoryRepository,
        TypeRepository $typeRepository,
        CategoryRepository $categoryRepository,
        Request $request,
        Security $security
    ): Response {
        $users = $this->getUser();
        $types = $typeRepository->findAll();
        $categories = $categoryRepository->findAll();
        $dataMemories = $memoryRepository->findAll();
        $user = $security->getUser();
        $category = [];

        $groupedMemories = $memoryRepository->findByUserGroupedByCategories($user);



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

        return $this->render('memory/memory.html.twig', [
            'donnees' => $dataMemories,
            'groupedMemories' => $groupedMemories,
            'types' => $types,
            'categories' => $categories,
            'user' => $users,
            'memoryForm' => $memoryForm->createView()
        ]);
    }



    #[Route("/update_memory_type/{id}", name: "update_memory_type", methods: "POST")]
    public function updateMemoryType(Request $request, MemoryRepository $memoryRepository, int $id): Response
    {

        $memory = $this->entityManager->getRepository(Memory::class)->find($id);


        if (!$memory) {
            return $this->json('No project found for id' . $id, 404);
        }

        $newTypeId = $request->request->get('id');
        $requestData = json_decode($request->getContent(), true);
        $newTypeId = $requestData['id'] ?? null;

        $type = null;
        if ($newTypeId !== null) {
            $type = $this->entityManager->getRepository(Type::class)->find($newTypeId);
        }
        if ($type instanceof Type) {
            $memory->setType($type);

            $this->entityManager->persist($memory);
            $this->entityManager->flush();

            $data = [
                'id' => $memory->getId(),
                'newType' => $type->getName(),
            ];

            return $this->json($data);
        } else {
            return $this->json(['message' => 'Invalid or missing type ID'], 400);
        }
    }


    #[Route("/update_memory_category/{id}", name: "update_memory_category", methods: "POST")]
    public function updateMemoryCategory(Request $request, MemoryRepository $memoryRepository, int $id): Response
    {

        $memory = $this->entityManager->getRepository(Memory::class)->find($id);


        if (!$memory) {
            return $this->json('No project found for id' . $id, 404);
        }

        $newCategoryId = $request->request->get('id');
        $requestData = json_decode($request->getContent(), true);
        $newCategoryId = $requestData['id'] ?? null;

        $category = null;
        if ($newCategoryId !== null) {
            $category = $this->entityManager->getRepository(Category::class)->find($newCategoryId);
        }
        if ($category instanceof Category) {
            $memory->setCategory($category);

            $this->entityManager->persist($memory);
            $this->entityManager->flush();

            $data = [
                'id' => $memory->getId(),
                'newCategory' => $category->getName(),
            ];

            return $this->json($data);
        } else {
            return $this->json(['message' => 'Invalid or missing type ID'], 400);
        }
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


        if ($newTitle !== null) {
            $memory->setTitle($newTitle);
        }

        if ($newDescription !== null) {
            $memory->setDescription($newDescription);
        }


        $entityManager->persist($memory);
        $entityManager->flush();


        $data = [
            [
                'id' => $memory->getId(),
                'title' => $memory->getTitle(),
                'description' => $memory->getDescription(),
                'newTitle' => $newTitle,
                'newDescription' => $newDescription,
                'request' => $request
            ]
        ];
        return $this->json($data);
    }
}