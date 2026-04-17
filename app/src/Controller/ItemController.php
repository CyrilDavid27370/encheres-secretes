<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\OfferFormType;
use App\Repository\CategoryRepository;
use App\Repository\ItemsRepository;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class ItemController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ItemsRepository $itemsRepository,CategoryRepository $categoryRepository): Response
    {
        $categoryId = $request->query->get('category');
        $search = $request->query->get('search');
        $categories = $categoryRepository->findAll();

        if ($search) {
            $items = $itemsRepository ->findBySearch($search);
        } elseif ($categoryId) {
            $items = $itemsRepository->findByCategory($categoryId);
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
    public function show(int $id, ItemsRepository $itemsRepository, OfferRepository $offerRepository, EntityManagerInterface $em, Request $request): Response
    {
    $item = $itemsRepository->find($id);

        if (!$item) {
            throw $this->createNotFoundException('Objet introuvable');
        }

        $form = null;

        if ($this->getUser() && $item->getStatus() === 'published') {
            $existingOffer = $offerRepository->findUserOfferForItem(
                $this->getUser(),
                $item,
            );

        if (!$existingOffer) {
            $offer = new Offer();
            $form = $this->createForm(OfferFormType::class, $offer);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($offer->getAmount() <= $item->getStartingPrice()) {
                    $this->addFlash('error', 'Votre offre doit être supérieure au prix de départ.');
                } else {
                    $offer->setUser($this->getUser());
                    $offer->setItem($item);
                    $em->persist($offer);
                    $em->flush();
                    $this->addFlash('success', 'Votre enchère a été placée avec succès !');
                }
                return $this->redirectToRoute('app_item_show', ['id' => $id]);
            }
        }
    }

                return $this->render('item/show.html.twig', [
                'item' => $item,
                'form' => $form?->createView(),
    ]);
}
}
