<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\BasketRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class OrderController extends AbstractController
{
    #[Route('/makeOrder/{basketId}', name: 'app_make_order')]
    public function makeOrder(
        $basketId,
        AuthorizationCheckerInterface $authorizationChecker,
        BasketRepository $basketRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        OrderRepository $orderRepository): Response
    {
        $authorized = $authorizationChecker->isGranted('ROLE_USER');
        if(!$authorized){
            $this->redirect('/signIn');
            exit();
        }

        $user = $this->getUser();

        if(is_null($user)){
            throw new UserNotFoundException();
        }

        $basket = $basketRepository->find($basketId);
        if(is_null($basket)){
            throw new NotFoundHttpException();
        }

        if($basket->getBasketTotalPrice() <= 0){
            return $this->render('bundles/TwigBundle/Exception/error.html.twig',
                ['status_code' =>'Basket is empty',
                    'status_text' => 'Put some product into',
                    'authorized'=>$authorized,
                    'user'=>$user,]);
        }
        if(!is_null($orderRepository->findByBasketId($basketId))){
            return $this->render('bundles/TwigBundle/Exception/error.html.twig',
                ['status_code' =>'Order is already exist',
                    'status_text' => 'Make new order',
                    'authorized'=>$authorized,
                    'user'=>$user,]);
        }

        $order = new Order();
        $order->setUserId($user->getId());
        $order->setBasketId($basketId);
        $order->setCreationDate(new \DateTime());
        $order->setStatus('CREATED');


        $user = $userRepository->find($user->getId());
        $user->setCurrentBasketId(null);

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->render('order/index.html.twig',
        [
            'authorized'=>$authorized,
            'user'=>$user,
        ]);
    }

    const ORDER_STATUS = [
        'CREATED',
        'PAYED',
        'COMPLETED',
        'FAILED'
    ];
}
