<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\Listeners\CustomExceptionListener.php

namespace Mana\ClientBundle\Listeners;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Description of CustomExceptionListener
 *
 * @author George
 */
class CustomExceptionListener
{

    private $recipient;
    private $twig;

    public function __construct($twig, $recipient)
    {
        $this->recipient = $recipient;
        $this->twig = $twig;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        }
        else {
            $statusCode = false;
        }

        $data = array(
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        );

        if (404 === $statusCode) {
            $content = $this->twig->render('ManaClientBundle:Default:error404.html.twig');
        }
        else {
            $subject = 'Project MANA error';
            $to = $this->recipient;
            $messge = $this->twig->render('ManaClientBundle:Default:error_mail.html.twig', array(
                'error' => $data,
            ));
            $headers = 'From: error_prone@projectmana.org' . "\n";

            $wasSent = mail($to, $subject, $messge, $headers);

            $good = 'An error has occurred; support has been notified.';
            $bad = "Please contact support regarding this message";

            $message = ($wasSent) ? $good : $bad;

            $content = $this->twig->render('ManaClientBundle:Default:message.html.twig', array(
                'message' => $message
            ));
        }
        $response = new Response($content);
        $event->setResponse($response);
    }

}
