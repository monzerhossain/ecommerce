<?php
/**
 * Created by PhpStorm.
 * User: monzer
 * Date: 8/14/21
 * Time: 11:07 AM
 */

namespace MyCommerce\ProductBundle\Controller;

use JMS\Serializer\SerializerBuilder;
use MyCommerce\ProductBundle\Exception\InvalidDataException;
use MyCommerce\ProductBundle\Exception\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use MyCommerce\ProductBundle\Service\ProductService;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/product", name="product_index", methods={"GET","HEAD"})
     */
    public function index(ProductService $productService): Response
    {
        try {
            $serializer = SerializerBuilder::create()->build();
            $products = $productService->getProducts();
            return new Response($serializer->serialize($products,'json'));
        }
        catch (ProductNotFoundException $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 404);
        }
        catch (\Exception $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/product/retrieve/{id}", name="retrieve_product", methods={"GET","HEAD"})
     */
    public function retrieve(int $id,  ProductService $productService): Response
    {
        try {
            $serializer = SerializerBuilder::create()->build();
            $product = $productService->findProduct($id);
            return new Response($serializer->serialize($product,'json'));
        }
        catch (ProductNotFoundException $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 404);
        }
        catch (\Exception $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 500);
        }

    }


    /**
     * @Route("/api/product/create", name="create_product", methods={"POST"})
     */
    public function createProduct(Request $request, ProductService $productService): Response
    {
        try {
            $content = $request->getContent();
            $productData = json_decode($content, true);
            $product = $productService->createProduct($productData);
            $serializer = SerializerBuilder::create()->build();
            return new Response($serializer->serialize($product,'json'));
        }
        catch (\JsonException $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 400);
        }
        catch (InvalidDataException $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 400);
        }
        catch (\Exception $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/product/update", name="update_product", methods={"PUT"})
     */
    public function updateProduct(Request $request, ProductService $productService): Response
    {
        try {
            $content = $request->getContent();
            $productData = json_decode($content, true);
            $product = $productService->updateProduct($productData);
            $serializer = SerializerBuilder::create()->build();
            return new Response($serializer->serialize($product,'json'));
        }
        catch (\JsonException $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 400);
        }
        catch (ProductNotFoundException $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 404);
        }
        catch (InvalidDataException $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 400);
        }
        catch (\Exception $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/product/delete/{id}", name="delete_product", methods={"DELETE"})
     */
    public function deleteProduct($id, ProductService $productService): Response
    {
        try {
            $result = $productService->deleteProduct($id);
            $serializer = SerializerBuilder::create()->build();
            return new Response($serializer->serialize($result,'json'));
        }
        catch (ProductNotFoundException $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 404);
        }
        catch (\Exception $ex){
            return new JsonResponse(["status" => 'error', 'message' => $ex->getMessage()], 500);
        }
    }
}


