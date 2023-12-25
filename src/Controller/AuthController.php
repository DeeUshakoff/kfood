<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AuthController extends AbstractController
{
    #[Route('/signIn', name: 'app_auth_sign_in_page', methods: ['GET'])]
    public function signInPage(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/signUp', name: 'app_auth_sign_up_page', methods: ['GET'])]
    public function signUpPage(): Response
    {
        return $this->render('signup/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    #[Route('/signUp', name: 'app_auth_sign_up', methods: ['POST'])]
    public function signUp(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        Security $security,
        UserRepository $userRepository): Response
    {
        $email = $request->request->get('email');
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $confirmPassword = $request->request->get('confirmPassword');
        $user = new User();
        if($confirmPassword !== $password){
            throw new InvalidPasswordException();
        }
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        $errorMessages = [];

        $passwordConstraints = new Assert\PasswordStrength(
            minScore: PasswordStrength::STRENGTH_WEAK,
            message: 'Your password is very weak');

        foreach ($validator->validate($password, $passwordConstraints) as $error){
            $errorMessages[] = $error->getMessage();
        }
        if(!is_null($userRepository->findOneByEmail($email))){
            $errorMessages[] = 'User with this email already exist';
        }

        if (count($errors) > 0 || count($errorMessages) > 0) {
            foreach ($errors as $error){
                $errorMessages[] = $error->getMessage();
            }
            return $this->render('signup/index.html.twig', [
                'errors'=> $errorMessages,
            ]);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $security->login($user);
        return $this->redirect('/menu');
    }

    #[Route('/signIn', name: 'app_auth_sign_in', methods: ['POST'])]
    public function signIn(
        Request $request,
        UserRepository $repository,
        Security $security,
        UserPasswordHasherInterface $passwordHasher): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = $repository->findOneByEmail($email);
        $errorMessages = [];
        if (is_null($user)) {
            $errorMessages[] = 'User not found';
            return $this->render('login/index.html.twig', [
                'errors'=> $errorMessages,
            ]);
        }
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            $errorMessages[] = 'Invalid password';
            return $this->render('login/index.html.twig', [
                'errors'=> $errorMessages,
            ]);
        }
        $security->login($user);
        $this->redirect('/');
        exit();
    }
}
