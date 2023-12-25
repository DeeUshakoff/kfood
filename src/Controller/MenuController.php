<?php

namespace App\Controller;

use App\Repository\ProductImageRepository;
use App\Repository\ProductIngredientsRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTagsRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use function Symfony\Component\Translation\t;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(
        AuthorizationCheckerInterface $authorizationChecker,
        ProductRepository $productRepository,
        TagRepository $tagRepository,
        ProductIngredientsRepository $productIngredientsRepository,
        ProductTagsRepository $productTagsRepository,
        ProductImageRepository $imageRepository): Response
    {
        $authorized = $authorizationChecker->isGranted('ROLE_USER');


        $products = $productRepository->findAll();
        $menu = [];
        foreach ($products as $product){
            $productId = $product->getId();
            $menuItem = [];
            $menuItem['imagePath'] = '';
            $image = $imageRepository->findOneByProductId($productId);

            if(!is_null($image)){
                $menuItem['imagePath'] = $image->getImagePath();
            }
            foreach ($productTagsRepository->findByProductId($productId) as $productTag){
                $tag = $tagRepository->find($productTag->getTagId());
                if($tag->getName() == 'Ingredient'){
                    continue(2);
                }
                $menuItem['tags'][] = $tag;
            }
            $menuItem['product'] = $product;
            $menuItem['ingredients'] = $productIngredientsRepository->findByProductId($productId);
            $menu[] = $menuItem;
        }

        $foodList = $this->filterMenuByTag('Food', $menu);
        $drinksList = $this->filterMenuByTag('Drink', $menu);

        $user = $this->getUser();
        return $this->render('menu/index.html.twig', [
            'controller_name' => 'MenuController',
            'menu' => $menu,
            'food' => $foodList,
            'drinks' => $drinksList,
            'authorized' => $authorized,
            'user'=>$user,
        ]);
    }
    function filterMenuByTag($tagName, $menu){

        $result = [];

            foreach ($menu as $menuItem){
                foreach ($menuItem['tags'] as $tag)
                if($tag->getName() == $tagName){
                    $result[] = $menuItem;
                }
            }
            return $result;
    }
}
