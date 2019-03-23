<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\User;
use App\Form\UserEmailType;
use App\Form\NewUserType;
use App\Services\EnvService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/register")
 */
class RegistrationController extends AbstractController {

    /**
     * @Route("/invite/{token}", name="complete_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token = null) {
        $em = $this->getDoctrine()->getManager();
        $invited = $em->getRepository('App:Invitation')->findOneBy(['token' => $token]);

        // if bogus token data is presented
        if (null === $invited) {
            $this->addFlash(
                    'danger',
                    'Invalid registration data'
            );

            return $this->redirectToRoute('home');
        }

        $email = $invited->getEmail();
        $existingUser = $em->getRepository('App:User')->findOneBy(['email' => $email]);

        //if $invited has already registered
        if (null !== $existingUser) {
            $this->addFlash(
                    'danger',
                    'User has already registered'
            );

            return $this->redirectToRoute('home');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFname($invited->getFname());
        $user->setSname($invited->getSname());
        $user->setUsername($invited->getUsername());
        $form = $this->createForm(NewUserType::class, $user);


        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $user->setPassword(
                    $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                    )
            );
            $user->setEnabled(true);
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);

            // remove new user from invitation table
            $invitee = $em->getRepository('App:Invitation')->findOneBy(['email' => $user->getEmail()]);
            $em->remove($invitee);

            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash(
                    'success',
                    'You are now registered and may log in'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render(
                        'Registration/register.html.twig',
                        array('form' => $form->createView(),
                            'headerText' => 'Create new user',
                        )
        );
    }

    /**
     * Render an form to submit email address
     * 
     * @Route("/forgot", name="register_forgot")
     */
    public function forgotPassword(Request $request, \Swift_Mailer $mailer, EnvService $env) {
        $form = $this->createForm(UserEmailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->request->get('user_email')['email'];
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('App:User')->findOneBy(['email' => $email]);
            if (null === $user) {
                $this->addFlash(
                        'danger',
                        'Email address not found'
                );

                return $this->redirectToRoute('home');
            }
            $token = md5(uniqid(rand(), true));

            $sender = $this->getParameter('swiftmailer.sender_address');
            $message = (new \Swift_Message('Project MANA forgotten password'))
                    ->setFrom($sender)
                    ->setTo($email)
                    ->setBody(
                    $this->renderView(
                            'Email/forgotten.html.twig',
                            [
                                'fname' => $user->getFname(),
                                'token' => $token,
                            ]
                    ),
                    'text/html'
                    )
            ;
            $mailer->send($message);
            $this->addFlash(
                    'success',
                    'Email sent to address provided'
            );

            $invitee = new Invitation();
            $invitee->setFname($user->getFname());
            $invitee->setSname($user->getSname());
            $invitee->setEmail($user->getEmail());
            $invitee->setToken($token);

            $em->persist($invitee);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('Registration/forgot.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Request forgotten password form'
        ]);
    }

    /**
     * @Route("/reset/{token}", name="reset_password")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token = null) {
        // for when either a logged in user or an unknown person: no token
        $em = $this->getDoctrine()->getManager();
        // make sure we're working with a logged in user
        if (null === $token) {
            $user = $this->getUser();
            if (null === $user) {
                $this->addFlash(
                        'danger',
                        'User not found'
                );

                return $this->redirectToRoute('home');
            }
        } else {
            // possible forgotten password user with token
            $person = $em->getRepository('App:Invitation')->findOneBy(['token' => $token]);
            if (null === $person) {
                $this->addFlash(
                        'danger',
                        'User not found'
                );

                return $this->redirectToRoute('home');
            }
            $user = $em->getRepository('App:User')->findOneBy(['email' => $person->getEmail()]);
        }
        $form = $this->createForm(NewUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $user->setPassword(
                    $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                    )
            );
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash(
                    'success',
                    'Your password has been updated'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('Registration/register.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Set new password',
        ]);
    }

}
