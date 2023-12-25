<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use function PHPUnit\Framework\isJson;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Column]
    private array $products = [];



    public function addProduct(Product $product): void
    {

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $this->products[$product->getId()]['product'] = $serializer->serialize($product, 'json');
        if(!isset($this->products[$product->getId()]['count'])){
            $this->products[$product->getId()]['count'] = 1;
        }
        else{
        $this->products[$product->getId()]['count']++;
        }
    }
    public function removeProduct(Product $product): void
    {

        if(isset($this->products[$product->getId()]['count'])){
            if($this->products[$product->getId()]['count'] - 1 > 0){

                $this->products[$product->getId()]['count']--;
            }
            else{
                unset($this->products[$product->getId()]);
            }
        }
    }
    public function getBasketTotalPrice():int{
        $result = 0;
        $products = $this->getProducts();
        foreach ($products as $product){
            $result+=$product['product']['price'] * $product['count'];
        }
        return $result;
    }
    public function getBasketSize():int{
        $result = 0;
        foreach ($this->products as $product){
            $result+=$product['count'];
        }
        return $result;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getProducts(): array
    {
        $decodedProducts = $this->products;
        foreach ($decodedProducts as &$item){
            $item['product'] = json_decode($item['product'], true);
        }

        return $decodedProducts;
    }

    public function setProducts(array $products): static
    {
        $this->products = $products;

        return $this;
    }

}
