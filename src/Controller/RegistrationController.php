<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedAvatar = $form->get('avatar')->getData();
            $user = $form->getData();
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));

            if ($uploadedAvatar) {
                $fileNameAvatar = uniqid("uploads/", true) . '.' .$uploadedAvatar->guessExtension();

                try {
                    $uploadedAvatar->move(
                        $this->getParameter('images_directory'),
                        $fileNameAvatar
                    );
                }
                catch (FileException $e) {

                }
            } else {
                $fileNameAvatar = 'uploads/avatar-default.png';
            }

            $user->setAvatar($fileNameAvatar);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
