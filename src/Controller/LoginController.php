<?php

namespace App\Controller;


use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class LoginController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        JWTTokenManagerInterface $JWTTokenManager
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    /**
     * @Route("/users/login", methods={"POST"}, name="app_login")
     */
    public function loginAction(Request $request)
    {
        $credentials = json_decode($request->getContent());

        $user = $this->userRepository->findOneBy(['email' => $credentials->user->email]);

        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $credentials->user->password)) {
            return new JsonResponse([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return new JsonResponse([
            'user' => [
                'email' => $user->getEmail(),
                'token' => $this->JWTTokenManager->create($user)
            ]
        ]);
    }
}
