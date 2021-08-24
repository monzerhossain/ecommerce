<?php
/**
 * Created by PhpStorm.
 * User: monzer
 * Date: 8/15/21
 * Time: 5:19 PM
 */

namespace MyCommerce\ProductBundle\Service;

use App\Entity\Attribute;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use MyCommerce\ProductBundle\Exception\InvalidDataException;
use MyCommerce\ProductBundle\Exception\ProductNotFoundException;
use Psr\Log\LoggerInterface;


class ProductService{

    var $doctrine;
    var $logger;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }

    private function validateData($productData)
    {
       if(!array_key_exists('name', $productData) || empty($productData['name']))
           throw new InvalidDataException("Product name is required");

    }

    /**
     * @param $productData
     * @return Product
     * @throws \Exception
     */
    public function createProduct($productData) : Product
    {
        try {

            $this->validateData($productData);

            $entityManager = $this->doctrine->getManager();

            $product = new Product();
            $product->setName($productData["name"]);
            $product->setDescription($productData["description"]);

            foreach ($productData["categories"] as $categoryData) {
                $category = $entityManager->getRepository(Category::class)->findOneBy(array('name' => $categoryData["name"]));
                if (!$category) {
                    $category = new Category();
                    $category->setName($categoryData["name"]);
                    $category->setDescription($categoryData["description"]);
                    $entityManager->persist($category);
                }
                $product->addCategory($category);
            }

            foreach ($productData["attributes"] as $attributeData) {
                $attribute = new Attribute();
                $attribute->setName($attributeData["name"]);
                $attribute->setValue($attributeData["value"]);
                $entityManager->persist($attribute);
                $product->addAttribute($attribute);
            }

            foreach ($productData["images"] as $imageData) {
                $image = new Image();
                $image->setUrl($imageData["url"]);
                $image->setHeight($imageData["height"]);
                $image->setWeight($imageData["weight"]);
                $entityManager->persist($image);
                $product->addImage($image);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $id = $product->getId();
            $this->logger->info("Product with $id created.");


            return $product;
        }
        catch (\Exception $ex){
            $this->logger->error("Failed to create product. " . $ex->getMessage());
            throw  $ex;
        }
    }

    /**
     * @param $productData
     * @return Product
     * @throws \Exception
     */
    public function updateProduct($productData) : Product
    {
        try {

            $id = $productData["id"];
            $product = $this->findProduct($id);

            $this->validateData($productData);

            $entityManager = $this->doctrine->getManager();

            $product->setName($productData["name"]);
            $product->setDescription($productData["description"]);

            foreach ($productData["categories"] as $categoryData) {
                $category = $entityManager->getRepository(Category::class)->findOneBy(array('id' => array_key_exists("id", $categoryData)?$categoryData["id"]:"") );
                if (!$category)
                    $category = new Category();

                $category->setName($categoryData["name"]);
                $category->setDescription($categoryData["description"]);
                $entityManager->persist($category);

                $product->addCategory($category);
            }

            foreach ($productData["attributes"] as $attributeData) {

                $attribute = $entityManager->getRepository(Attribute::class)->findOneBy(array('id' => array_key_exists("id", $attributeData)?$attributeData["id"]:""));

                if(!$attribute)
                    $attribute = new Attribute();

                $attribute->setName($attributeData["name"]);
                $attribute->setValue($attributeData["value"]);
                $entityManager->persist($attribute);
                $product->addAttribute($attribute);
            }

            foreach ($productData["images"] as $imageData) {

                $image = $entityManager->getRepository(Image::class)->findOneBy(array('id' => array_key_exists("id", $imageData)?$imageData["id"]:""));
                if(!$image)
                    $image = new Image();
                $image->setUrl($imageData["url"]);
                $image->setHeight($imageData["height"]);
                $image->setWeight($imageData["weight"]);
                $entityManager->persist($image);
                $product->addImage($image);
            }

            $entityManager->flush();
            $this->logger->info("Product with $id updated.");

            return $product;
        }
        catch (\Exception $ex){
            $this->logger->error("Failed to update product with Id $id . " . $ex->getMessage());
            throw  $ex;
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getProducts() : array
    {
        try {
            $entityManager = $this->doctrine->getManager();
            /** @var Product $product[] */
            $products = $entityManager->getRepository(Product::class)->findAll();
            return $products;
        }
        catch (\Exception $ex){
            $this->logger->error("Failed to get the products. " . $ex->getMessage());
            throw  $ex;
        }

    }

    /**
     * @param int $id
     * @return Product
     * @throws \Exception
     */
    public function findProduct(int $id) : Product
    {
        try {
            $entityManager = $this->doctrine->getManager();
            /** @var Product $product */
            $product = $entityManager->getRepository(Product::class)->find($id);
            if($product) {
                $this->logger->info("Product with Id $id found.");
                return $product;
            }
            else
                throw new ProductNotFoundException("Product with Id $id is not available in the system.");
        }
        catch (\Exception $ex){
            $this->logger->error("Failed to find the product with id $id. " . $ex->getMessage());
            throw  $ex;
        }

    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteProduct(int $id) : bool
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $product = $this->findProduct($id);
            if($product) {
                $entityManager->remove($product);
                $entityManager->flush();

                $this->logger->info("Product with Id $id deleted.");
                return true;
            }
            else
                throw new ProductNotFoundException("Product with Id $id is not available in the system.");
        }
        catch (\Exception $ex){
            $this->logger->error("Failed to delete the product with id $id. " . $ex->getMessage());
            throw  $ex;
        }

    }

}