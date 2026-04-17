<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ItemsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ItemController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ItemsRepository $itemsRepository,CategoryRepository $categoryRepository): Response
    {
        $categoryId = $request->query->get('category');
        $categories = $categoryRepository->findAll();

        if ($categoryId) {
            $items = $itemsRepository ->findByCategory($categoryId);
        } else {
            $items = $itemsRepository->findPublished();
        }
        return $this->render('item/index.html.twig', [
            'items' => $items,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
        ]);
    }

    #[Route('/item/{id}', name: 'app_item_show')]
public function show(int $id, ItemsRepository $itemsRepository): Response
{
    $item = $itemsRepository->find($id);

    if (!$item) {
        throw $this->createNotFoundException('Objet introuvable');
    }

    return $this->render('item/show.html.twig', [
        'item' => $item,
    ]);
}
}
