<?php

namespace App\Controller;

use App\Repository\ProductImageRepository;
use App\Repository\ProductIngredientsRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTagsRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductDetailsController extends AbstractController
{
    #[Route('/product/{id}', name: 'app_product_details')]
    public function index(
        $id,
        AuthorizationCheckerInterface $authorizationChecker,
        Request $request,
        ProductRepository $productRepository,
        TagRepository $tagRepository,
        ProductIngredientsRepository $productIngredientsRepository,
        ProductTagsRepository $productTagsRepository,
        ProductImageRepository $imageRepository
    ): Response
    {

        $authorized = $authorizationChecker->isGranted('ROLE_USER');
        $product = $productRepository->find($id);


        if(is_null($product)){
            throw new NotFoundHttpException();
        }
        $productId = $product->getId();
        $product =['product'=>$product];

        foreach ($productTagsRepository->findByProductId($productId) as $productTag){
            $ingredient = $tagRepository->find($productTag->getTagId());
            if($ingredient->getName() == 'Ingredient' & !$authorizationChecker->isGranted('ROLE_ADMIN')){
                throw new AccessDeniedException();
            }
            $product['tags'][] = $ingredient;
        }
        $product['ingredients'] = [];
        foreach ($productIngredientsRepository->findByProductId($productId) as $productIngredient){
            $ingredient = $productRepository->find($productIngredient->getIngredientId());

            $product['ingredients'][] = $ingredient;
        }
        $product['imagePaths'] = [];
        foreach ($imageRepository->findByProductId($productId) as $productImage){
            $product['imagePaths'][] = $productImage->getImagePath();
        }
        $user = $this->getUser();

        return $this->render('product_details/index.html.twig', [
            'controller_name' => 'ProductDetailsController',
            'authorized' => $authorized,
            'product' => $product,
            'user' => $user,
        ]);
    }
}
