<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function Symfony\Component\String\b;

class BasketController extends AbstractController
{
    #[Route('/basket', name: 'app_basket')]
    public function index(
        AuthorizationCheckerInterface $authorizationChecker,
        BasketRepository $basketRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
    ): Response
    {
        $authorized = $authorizationChecker->isGranted('ROLE_USER');

        if(!$authorized){
            return $this->redirectToRoute('app_auth_sign_in_page');
        }
        $user = $userRepository->find($this->getUser()->getId());

        $currentBasketId = $user->getCurrentBasketId();

        $basket = null;
        if(!is_null($currentBasketId)){
            $basket = $basketRepository->find($user->getCurrentBasketId());
        }

        if(is_null($basket)){
            $basket = new Basket();
            $basket->setUserId($user->getId());
            $entityManager->persist($basket);
            $entityManager->flush();

        }

        $basket = $basketRepository->findByUserId($user->getId());
        $basket = $basket[0];

        $user->setCurrentBasketId($basket->getId());
        $entityManager->flush();

        return $this->render('basket/index.html.twig', [
            'controller_name' => 'BasketController',
            'authorized' => $authorized,
            'basket' => $basket,
            'user' => $user,
        ]);
    }


    #[Route('/basket/addProduct/{id}', name: 'app_basket_add_product', methods: ['POST'])]
    public function addProduct(
        $id,
        AuthorizationCheckerInterface $authorizationChecker,
        BasketRepository $basketRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager

    ): Response
    {
        $authorized = $authorizationChecker->isGranted('ROLE_USER');

        if(!$authorized){
            throw new AccessDeniedException();
        }
        $basket = null;
        $basketId = $this->getUser()->getCurrentBasketId();
        $product = $productRepository->find($id);
        if(is_null($basketId)){
            $basket = new Basket();
            $basket->setUserId($this->getUser()->getId());
            $basket->addProduct($product);
            $entityManager->persist($basket);
            $entityManager->flush();
        }
        $basket = $basketRepository->find($basketId);

        $basket->addProduct($product);
        $entityManager->flush();

        return $this->json($basket);
    }

    #[Route('/basket/removeOneProduct/{id}', name: 'app_basket_remove_one_product', methods: ['POST'])]
    public function removeOneProduct(
        $id,
        AuthorizationCheckerInterface $authorizationChecker,
        BasketRepository $basketRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager

    ): Response
    {
        $authorized = $authorizationChecker->isGranted('ROLE_USER');

        if(!$authorized){
            throw new AccessDeniedException();
        }

        $basket = $basketRepository->find($this->getUser()->getCurrentBasketId());
        $product = $productRepository->find($id);
        if(is_null($basket)){
            throw new NotFoundHttpException();
        }
        $basket->removeProduct($product);
        $entityManager->flush();

        return $this->json($basket);
    }

    #[Route('/basket/get', name: 'app_basket_get', methods: ['GET'])]
    public function getBasket(
        AuthorizationCheckerInterface $authorizationChecker,
        BasketRepository $basketRepository,

    ): Response
    {
        $authorized = $authorizationChecker->isGranted('ROLE_USER');

        if(!$authorized){
            throw new AccessDeniedException();
        }

        $basketId = $this->getUser()->getCurrentBasketId();
        if(is_null($basketId)){
            throw new NotFoundHttpException();
        }
        $basket = $basketRepository->find($this->getUser()->getCurrentBasketId());

        if(is_null($basket)){
            throw new NotFoundHttpException();
        }

//        $decodedProducts = $basket->getProducts();
//        foreach ($decodedProducts as &$item){
//            $item['product'] = json_decode($item['product'], true);
//        }
//
//        $basket->setProducts($decodedProducts);
        return $this->json($basket);
    }
}
