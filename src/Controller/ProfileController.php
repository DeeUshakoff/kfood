<?php

namespace App\Controller;

use App\Repository\BasketRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        AuthorizationCheckerInterface $authorizationChecker,
        OrderRepository $orderRepository,
        BasketRepository $basketRepository,
    ): Response
    {
        $authorized = $authorizationChecker->isGranted('ROLE_USER');

        if(!$authorized){
            $this->redirect('/signIn');
            exit();
        }
        $user = $this->getUser();
        $orders = $orderRepository->findByUserId($user->getId());

        $ordersToRender = [];
        foreach ($orders as $order){
            $ordersItem = [];
            $ordersItem['order'] = $order;
            $ordersItem['basket'] = $basketRepository->find($order->getBasketId());
            $ordersToRender[] = $ordersItem;
        }

        return $this->render('profile/index.html.twig', [
            'authorized' => $authorized,
            'user' => $user,
            'orders' => $ordersToRender,
        ]);
    }

}
