<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/edit/profile')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ProfileController extends AbstractController
{
    public function editProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $fullPhoneNumber = $user->getFullPhoneNumber();
        $countryPrefix = substr($fullPhoneNumber, 0, -9);
        $phoneNumber = substr($fullPhoneNumber, -9);

        $form = $this->createForm(ProfileFormType::class, $user);

        $form->get('countryPrefix')->setData($countryPrefix);
        $form->get('phoneNumber')->setData($phoneNumber);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser && $existingUser !== $user) {
                $form->get('email')->addError(new FormError('Email is already in use.'));
            } else {
                $user->setEmail($email);
                $plainPassword = $form->get('plainPassword')->getData();
                if ($plainPassword) {
                    $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Profile updated successfully.');

                return $this->redirectToRoute('app_edit_profile');
            }
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }
}
