<?php

namespace App\Controller;

use App\AbstractVendor\Http\AbstractController;
use App\AbstractVendor\Http\JsonResponse;
use App\AbstractVendor\Http\Request;
use App\AbstractVendor\Http\Response;
use App\AbstractVendor\ORM\EntityManager;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\User\Exception\Base2faException;
use App\Service\User\Exception\IncorrectValidationKeyException;
use App\Service\User\Exception\TooManyAttemptsException;
use App\Service\User\Exception\UserNotFoundException;
use App\Service\User\User2faService;
use App\Service\User\UserService;

class UserApiController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private EntityManager $entityManager,
        private User2faService $user2faService,
        private UserRepository $userRepository,
    ) {}

    #[Route('/user/edit_prop', name: 'update_security_property')]
    public function updateSecurityProp(Request $request): Response
    {
        $formType = $this->createForm(UserType::class);
        $formType->handleRequest($request);

        $user = $this->userRepository->find($formType->get('id'));

        if (!$user) {
            throw new UserNotFoundException("User not found");
        }

        if (!$this->user2faService->isApprove($user)) {
            return $this->redirectToRoute('validate_update');
        }

        $user = $this->userService->updateSecurityProp($user, $formType, $formType->get('genericUserData'));
        $this->entityManager->flush();

        return new JsonResponse($user);
    }

    #[Route('/user/validate', name: 'validate_update')]
    public function validateUpdate(Request $request): Response
    {
        $formType = $this->createForm(UserType::class);
        $formType->handleRequest($request);

        if ($this->user2faService->isApprove($formType->get('id'))) {
            return $this->redirectToRoute('validate_update');
        }

        $validationKey = $request->get('validation_key');

        $user = $this->userRepository->find($formType->get('id'));
        try {
            $this->user2faService->validate($user, $validationKey);
        } catch (Base2faException $e) {
            $this->user2faService->updateAttempt($user->id);

            return new JsonResponse([
                'status' => false,
                'error' => $e->getMessage()
            ], 422);
        }

        return $this->redirectToRoute('update_security_property');
    }
}
