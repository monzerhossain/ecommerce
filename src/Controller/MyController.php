<?php
/**
 * Created by PhpStorm.
 * User: monzer
 * Date: 8/14/21
 * Time: 11:07 AM
 */

namespace Ecommerce\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyController
{
    /**
     * @Route("/", name="hello")
     */
    public function hello(): JsonResponse
    {
        return new JsonResponse(['username' => 'monzer.hossain']);
    }
}
