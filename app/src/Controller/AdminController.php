<?php

namespace App\Controller;

use App\Entity\Items;
use App\Form\ItemFormType;
use App\Repository\ItemsRepository;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(ItemsRepository $itemsRepository): Response
    {
        $items = $itemsRepository->findAllWithOfferCount();

        return $this->render('admin/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/admin/item/{id}', name: 'app_admin_item_show')]
    public function show(int $id, ItemsRepository $itemsRepository, OfferRepository $offerRepository): Response
    {
        $item = $itemsRepository->find($id);

        if (!$item) {
            throw $this->createNotFoundException('Objet introuvable');
        }

        $offers = $offerRepository->findByItem($id);

        return $this->render('admin/show.html.twig', [
            'item' => $item,
            'offers' => $offers,
        ]);
    }

    #[Route('/admin/item/{id}/toggle', name: 'app_admin_toggle')]
    public function toggle(int $id, ItemsRepository $itemsRepository, OfferRepository $offerRepository, EntityManagerInterface $em): Response
    {
        $item = $itemsRepository->find($id);

        if (!$item) {
            throw $this->createNotFoundException('Objet introuvable');
        }

        if ($item->getStatus() === 'unpublished') {
            $item->setStatus('published');
        } elseif ($item->getStatus() === 'published') {
            $offers = $offerRepository->findByItem($id);
            if (count($offers) === 0) {
                $item->setStatus('unpublished');
            } else {
                $this->addFlash('error', 'Impossible de dépublier un objet avec des enchères.');
            }
        }

        $em->flush();
        return $this->redirectToRoute('app_admin_item_show', ['id' => $id]);
    }

    #[Route('/admin/item/{id}/close', name: 'app_admin_close', methods: ['POST'])]
    public function close(int $id, Request $request, ItemsRepository $itemsRepository, OfferRepository $offerRepository, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('close' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_item_show', ['id' => $id]);
        }

        $item = $itemsRepository->find($id);

        if (!$item || $item->getStatus() !== 'published') {
            throw $this->createNotFoundException('Objet introuvable ou non publié');
        }

        $bestOffer = $offerRepository->findBestOferForItem($id);

        if (!$bestOffer) {
            $this->addFlash('error', 'Impossible de clôturer sans enchère.');
            return $this->redirectToRoute('app_admin_item_show', ['id' => $id]);
        }

        $item->setStatus('closed');
        $item->setWinner($bestOffer->getUser());
        $item->setFinalPrice($bestOffer->getAmount());

        $em->flush();

        $this->addFlash('success', 'Enchère clôturée ! Gagnant : ' . $bestOffer->getUser()->getEmail());
        return $this->redirectToRoute('app_admin_item_show', ['id' => $id]);
    }

    #[Route('/admin/form/{id}', name: 'app_admin_item_form', defaults: ['id' => null])]
    public function form(?int $id, Request $request, ItemsRepository $itemsRepository): Response
    {
        if ($id) {
            $item = $itemsRepository->find($id);

            if (!$item) {
                throw $this->createNotFoundException('Objet introuvable');
            }
            $title = "Modifier un objet";
        } else {
            $item = new Items();
            $title = "Ajouter un objet";
        }

        $form = $this->createForm(ItemFormType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemsRepository->save($item);
            $this->addFlash('success', $id ? 'Objet modifié avec succès !' : 'Objet crée avec succès !');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/form.html.twig', [
            'form' => $form,
            'item' => $item,
            'title' => $title,
        ]);
    }

    #[Route('/admin/item/{id}/delete', name: 'app_admin_item_delete', methods: ['POST'])]
    public function delete(int $id,Request $request, ItemsRepository $itemsRepository): Response
    {
        $item = $itemsRepository->find($id);

        if (!$item) {
            throw $this->createNotFoundException('Objet introuvable');
        }

        if (!$this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin');
        }

        $itemsRepository->remove($item);
        $this->addFlash('success', 'Objet supprimé avec succès !');
        return $this->redirectToRoute('app_admin');
    }
}
