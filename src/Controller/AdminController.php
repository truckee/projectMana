<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Form\InvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{

    /**
     * Invitation creates a new user without a password
     *
     * @Route("/invite", name="user_invitation")
     */
    public function invite(Request $request, \Swift_Mailer $mailer)
    {
        $invite = new Invitation();
        $form = $this->createForm(InvitationType::class, $invite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation = $request->request->get('invitation');
            $email = $invitation['email'];
            $email = $invitation['email'];
            $em = $this->getDoctrine()->getManager();
            $userEmail = $em->getRepository('App:User')->findOneBy(['email' => $email]);
            $username = $em->getRepository('App:User')->findOneBy(['username' => $invitation['username']]);
            if (null !== $userEmail || null !== $username) {
                $this->addFlash(
                    'warning',
                    'Email or username already exists'
                );

                return $this->redirectToRoute('home');
            }
            
            $expiry = new \DateTime();
            $invite->setPasswordExpiresAt($expiry->add(new \DateInterval('PT3H')));

            $sender = $this->getParameter('swiftmailer.sender_address');
            $view = $this->renderView(
                'Email/invitation.html.twig',
                [
                                'fname' => $invitation['fname'],
                                'token' => $invitation['confirmationToken'],
                                'expires' => $expiry,
                                ]
                    );
            $message = (new \Swift_Message('Project MANA app invitation'))
                    ->setFrom($sender)
                    ->setTo($invitation['email'])
                    ->setBody(
                        $view,
                        'text/html'
                    )
            ;
            $mailer->send($message);

            $invite->setConfirmationToken($invitation['confirmationToken']);
            $em->persist($invite);
            $em->flush();
            $this->addFlash(
                'success',
                'Invitation sent'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('Security/invite.html.twig', [
                    'form' => $form->createView(),
                    'header' => 'Create a new app user',
        ]);
    }
}
