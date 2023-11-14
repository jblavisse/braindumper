<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MemoryRepository;

class MemoryController extends AbstractController
{
    
    #[Route('/', name: 'app_memory')]
    public function show(MemoryRepository $memoryRepository): Response
    {
        $memory = $memoryRepository
            ->findAll();


        return $this->render('memory/memory.html.twig', ['donnees' => $memory]);

    }
}