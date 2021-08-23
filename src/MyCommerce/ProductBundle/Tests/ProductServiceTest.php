<?php
/**
 * Created by PhpStorm.
 * User: monzer
 * Date: 8/21/21
 * Time: 10:38 PM
 */

namespace MyCommerce\ProductBundle\Test;

use App\Entity\Attribute;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use MyCommerce\ProductBundle\Exception\InvalidDataException;
use MyCommerce\ProductBundle\Exception\ProductNotFoundException;
use MyCommerce\ProductBundle\Service\MockHelper;
use MyCommerce\ProductBundle\Service\ProductService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

//include_once '../Service/ProductService.php';

class ProductServiceTest extends TestCase
{
    protected function setup(): void
    {
    }

    public function testFindProduct() : void
    {
        $id = 1;
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, Product::class  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $productService = new ProductService($doctrineMock, $loggerMock);
        $result = $productService->findProduct($id);

        $this->assertInstanceOf(Product::class, $result);

    }

    public function testFindProductNotExists() : void
    {
        $id = 1;
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, null  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $this->expectException(ProductNotFoundException::class);

        $productService = new ProductService($doctrineMock, $loggerMock);
        $result = $productService->findProduct($id);

    }

    public function testFindProductThrowsException() : void
    {
        $id = 1;
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, null  );
        $doctrineMock
            ->method('getManager')
            ->willThrowException(new \Exception("Cant find product."));
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $this->expectException(\Exception::class);

        $productService = new ProductService($doctrineMock, $loggerMock);
        $productService->findProduct($id);

    }

    public function testGetProducts() : void
    {
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, Product::class  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $productService = new ProductService($doctrineMock, $loggerMock);
        $result = $productService->getProducts();

        $this->assertTrue(is_array($result));
        $this->assertInstanceOf(Product::class, $result[0]);

    }

    public function testGetProductsThrowsException() : void
    {
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, null  );
        $doctrineMock
            ->method('getManager')
            ->willThrowException(new \Exception("Cant get products."));
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $this->expectException(\Exception::class);

        $productService = new ProductService($doctrineMock, $loggerMock);
        $productService->getProducts();

    }

    public function testDeleteProduct() : void
    {
        $id = 1;
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, Product::class  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $entityManagerMock = $doctrineMock->getManager();
        $entityManagerMock
            ->expects($this->once())
            ->method('remove');
        $entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $productService = new ProductService($doctrineMock, $loggerMock);
        $result = $productService->deleteProduct($id);

        $this->assertTrue($result);

    }

    public function testDeleteProductNotExists() : void
    {
        $id = 1;
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, null  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $entityManagerMock = $doctrineMock->getManager();
        $entityManagerMock
            ->expects($this->never())
            ->method('remove');
        $entityManagerMock
            ->expects($this->never())
            ->method('flush');

        $this->expectException(ProductNotFoundException::class);


        $productService = new ProductService($doctrineMock, $loggerMock);
        $productService->deleteProduct($id);

    }

    public function testDeleteProductThrowsException() : void
    {
        $id = 1;
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, Product::class  );
        $doctrineMock
            ->method('getManager')
            ->willThrowException(new \Exception("Cant delete product."));
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $this->expectException(\Exception::class);

        $productService = new ProductService($doctrineMock, $loggerMock);
        $productService->deleteProduct($id);

    }

    private function getProductData() : array
    {
        return json_decode(
            "{
  \"name\": \"Sample\",
  \"description\": \"Its test product\",
  \"categories\": [
    {
      \"name\": \"cat1\",
      \"description\": \"test category\"
    }
  ],
  \"attributes\": [
    {
      \"name\": \"price\",
      \"value\": \"100.00\"
    },
    {
      \"name\": \"gst\",
      \"value\": \"10.00\"
    }
  ],
  \"images\": [
    {
      \"url\": \"abc.com\",
      \"height\": 15.0,
      \"weight\": 20.0
    },
		{
      \"url\": \"xyz.com\",
      \"height\": 10.0,
      \"weight\": 20.0
    }
  ]
}",
            true
        );
    }

    public function testCreateProduct() : void
    {
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, null  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $entityManagerMock = $doctrineMock->getManager();
        $entityManagerMock
            ->expects($this->any())
            ->method('persist');
        $entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $productData = $this->getProductData();
        $productService = new ProductService($doctrineMock, $loggerMock);
        $result = $productService->createProduct($productData);

        $this->assertInstanceOf(Product::class, $result);


    }

    public function testCreateProductThrowsException() : void
    {
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, null  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $doctrineMock
            ->method('getManager')
            ->willThrowException(new \Exception("Cant create product."));

        $this->expectException(\Exception::class);

        $productData = $this->getProductData();
        $productService = new ProductService($doctrineMock, $loggerMock);
        $productService->createProduct($productData);

    }

    private function getInvalidProductData() : array
    {
        return json_decode(
            '{
  "name": "",
  "description": "Its test product",
  "categories": [
    {
      "name": "cat1",
      "description": "test category"
    }
  ],
  "attributes": [
    {
      "name": "price",
      "value": "100.00"
    },
    {
      "name": "gst",
      "value": "10.00"
    }
  ],
  "images": [
    {
      "url": "abc.com",
      "height": 15.0,
      "weight": 20.0
    },
		{
      "url": "xyz.com",
      "height": 10.0,
      "weight": 20.0
    }
  ]
}',
            true
        );
    }

    public function testCreateProductInvalidData() : void
    {
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, null  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);


        $this->expectException(InvalidDataException::class);

        $productData = $this->getInvalidProductData();
        $productService = new ProductService($doctrineMock, $loggerMock);
        $productService->createProduct($productData);

    }

    private function getExistingProductData() : array
    {
        return json_decode(
            '{
  "id": 1,
  "name": "Sample",
  "description": "Its test product",
  "categories": [
    {
      "name": "cat1",
      "description": "test category"
    }
  ],
  "attributes": [
    {
      "name": "price",
      "value": "100.00"
    },
    {
      "name": "gst",
      "value": "10.00"
    }
  ],
  "images": [
    {
      "url": "abc.com",
      "height": 15.0,
      "weight": 20.0
    },
		{
      "url": "xyz.com",
      "height": 10.0,
      "weight": 20.0
    }
  ]
}',
            true
        );
    }

    public function testUpdateProduct() : void
    {
        $entityManagerMock = MockHelper::getEntityManagerMock($this,
            array(
                Product::class => MockHelper::getRepoMock($this, MockHelper::getEntityMock($this, Product::class)),
                Category::class => MockHelper::getRepoMock($this, MockHelper::getEntityMock($this, null)),
                Attribute::class => MockHelper::getRepoMock($this, MockHelper::getEntityMock($this, null)),
                Image::class => MockHelper::getRepoMock($this, MockHelper::getEntityMock($this, null))

            ));

        $doctrineMock = MockHelper::getDoctrineMock($this, $entityManagerMock, null, null  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $entityManagerMock
            ->expects($this->any())
            ->method('persist');
        $entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $productData = $this->getExistingProductData();
        $productService = new ProductService($doctrineMock, $loggerMock);
        $result = $productService->updateProduct($productData);

        $this->assertInstanceOf(Product::class, $result);


    }

    public function testUpdateProductNotExists() : void
    {
        $entityManagerMock = MockHelper::getEntityManagerMock($this,
            array(
                Product::class => MockHelper::getRepoMock($this, MockHelper::getEntityMock($this, null)),
                Category::class => MockHelper::getRepoMock($this, MockHelper::getEntityMock($this, null)),
                Attribute::class => MockHelper::getRepoMock($this, MockHelper::getEntityMock($this, null)),
                Image::class => MockHelper::getRepoMock($this, MockHelper::getEntityMock($this, null))

            ));

        $this->expectException(ProductNotFoundException::class);

        $doctrineMock = MockHelper::getDoctrineMock($this, $entityManagerMock, null, null  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $productData = $this->getExistingProductData();
        $productService = new ProductService($doctrineMock, $loggerMock);
        $productService->updateProduct($productData);


    }


    public function testUpdateProductThrowsException() : void
    {
        $doctrineMock = MockHelper::getDoctrineMock($this, null, null, null  );
        $loggerMock = MockHelper::getMock($this, LoggerInterface::class);

        $doctrineMock
            ->method('getManager')
            ->willThrowException(new \Exception("Cant update product."));

        $this->expectException(\Exception::class);

        $productData = $this->getExistingProductData();
        $productService = new ProductService($doctrineMock, $loggerMock);
        $productService->updateProduct($productData);

    }

}