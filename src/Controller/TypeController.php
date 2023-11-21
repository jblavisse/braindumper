<?php

namespace App\Controller;

use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TypeController extends AbstractController
{
    #[Route('/type', name: 'app_type')]
    public function getTypes(TypeRepository $typeRepo): Response
    {
        $types = $typeRepo->findAll();

        $data = [];
        foreach ($types as $type) {
            $data[] = [
                'id' => $type->getId(),
                'name' => $type->getName(),
            ];
        }

        return new JsonResponse($data);
    }
}