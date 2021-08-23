<?php
/**
 * Created by PhpStorm.
 * User: monzer
 * Date: 8/21/21
 * Time: 5:23 PM
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // ...

        return new JsonResponse(["status"=> 'error', "message" => "Access Denied! You don't have authorization to access this resource."], 403);
    }
}