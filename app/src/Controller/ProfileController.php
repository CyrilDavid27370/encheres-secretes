<?php

namespace App\Controller;

use App\Repository\ItemsRepository;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(OfferRepository $offerRepository, ItemsRepository $itemsRepository): Response
    {
        $user = $this->getUser();

        $offers = $offerRepository->findByUser($user);
        $wonItems = $itemsRepository->findWonByUser($user);

        return $this->render('profile/index.html.twig', [
            'offers' => $offers,
            'wonItems' => $wonItems,
        ]);
    }
}
