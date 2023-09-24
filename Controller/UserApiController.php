<?php

namespace App\Controller;

use App\AbstractVendor\Http\AbstractController;
use App\AbstractVendor\Http\JsonResponse;
use App\AbstractVendor\Http\Request;
use App\AbstractVendor\ORM\EntityManager;
use App\Form\UserType;
use App\Service\User\UserService;

class UserApiController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private EntityManager $entityManager,
    ) {}

    public function updateSecurityProp(Request $request): JsonResponse
    {
        $formType = $this->createForm(UserType::class);
        $formType->handleRequest($request);
        $user = $this->userService->updateSecurityProp($formType);

        $this->entityManager->flush();
    }
}