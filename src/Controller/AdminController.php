<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Form\InvitationType;
use App\Services\EnvService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController {

    /**
     * @Route("/invite", name="user_invitation")
     */
    public function invite(Request $request, \Swift_Mailer $mailer, EnvService $env) {
        $invite = new Invitation();
        $form = $this->createForm(InvitationType::class, $invite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation = $request->request->get('invitation');
            $sender = $this->getParameter('swiftmailer.sender_address');
            $message = (new \Swift_Message('Project MANA app invitation'))
                    ->setFrom($sender)
                    ->setTo($invitation['email'])
                    ->setBody(
                    $this->renderView(
                            'Email/invitation.html.twig',
                            [
                                'fname' => $invitation['fname'],
                                'token' => $invitation['token'],
                            ]
                    ),
                    'text/html'
                    )
            ;
            $mailer->send($message);
            
            $em = $this->getDoctrine()->getManager();
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
