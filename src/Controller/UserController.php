<?php


namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $this->em->persist($user);
        $this->em->flush();

        return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('EDIT', subject: 'user')]
    public function update(User $user, Request $request, #[CurrentUser] ?User $currentUser): JsonResponse
        {
            if ($user->getId() !== $currentUser->getId()) {
                return $this->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
            }

            $this->serializer->deserialize($request->getContent(), User::class, 'json', [
                'object_to_populate' => $user
            ]);

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $this->em->flush();

            return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
        }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('DELETE', subject: 'user')]
    public function delete(User $user, #[CurrentUser] ?User $currentUser): JsonResponse
        {
            if ($user->getId() !== $currentUser->getId()) {
                return $this->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
            }

            $this->em->remove($user);
            $this->em->flush();

            return $this->json(null, Response::HTTP_NO_CONTENT);
        }
}