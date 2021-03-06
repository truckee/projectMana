<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Controller;

use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Truckee\ProjectmanaBundle\Entity\Contact;
use Truckee\ProjectmanaBundle\Entity\Center;
use Truckee\ProjectmanaBundle\Form\ContactType;
use Truckee\ProjectmanaBundle\Form\SelectCenterType;
use Truckee\ProjectmanaBundle\Utilities\PdfService;

/**
 * Contact controller.
 *
 * @Route("/contact")
 */
class ContactController extends Controller
{
    use \Truckee\ProjectmanaBundle\Utilities\FYFunction;

    /**
     * Create a new Contact entity.
     *
     * @param int $id Household id
     *
     * @return Response
     *
     * @Route("/{id}/new", name="contact_new")
     */
    public function newAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $household = $em->getRepository('TruckeeProjectmanaBundle:Household')->find($id);
        $flash = $this->get('braincrafted_bootstrap.flash');
        if (!$household) {
            $flash->error('Unable to find Household ' . $id);

            return $this->redirectToRoute('home');
        }
        $contact = new Contact();
        $contact->setContactDate(date_create());
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formCriteria = $request->request->get('contact');
            $center = $em->getRepository('TruckeeProjectmanaBundle:Center')->find($formCriteria['center']);
            $county = $em->getRepository('TruckeeProjectmanaBundle:County')->find($center->getCounty());
            $contact->setCounty($county);
            $nContacts = count($household->getContacts());
            $first = ($nContacts > 0) ? 0 : 1;
            $contact->setFirst($first);
            $household->addContact($contact);
            $em->persist($household);
            $em->flush();
            $flash->alert("Contact added for household $id");

            return $this->redirectToRoute('contact_new', array('id' => $id));
        }

        return $this->render(
            'Contact/edit.html.twig',
            array(
                    'form' => $form->createView(),
                    'household' => $household,
                    'title' => 'New Contact',
        )
        );
    }

    /**
     * Edit an existing Contact entity.
     *
     * @param int $id Contact id
     *
     * @return Response
     *
     * @Route("/{id}/edit", name="contact_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('TruckeeProjectmanaBundle:Contact')->find($id);
        $flash = $this->get('braincrafted_bootstrap.flash');
        if (!$contact) {
            $flash->error('Unable to find contact ');

            return $this->redirectToRoute('home');
        }
        $searches = $this->get('mana.searches');
        $disabledOptions = $searches->getDisabledOptions($contact);
        $form = $this->createForm(
            ContactType::class,
            $contact,
            ['disabledOptions' => $disabledOptions]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contact);
            $em->flush();
            $hid = $contact->getHousehold()->getId();
            $flash->alert('Contact has been updated');

            return $this->redirectToRoute('contact_new', array('id' => $hid));
        }

        return $this->render(
            'Contact/edit.html.twig',
            array(
                    'household' => $contact->getHousehold(),
                    'form' => $form->createView(),
                    'contact' => $contact,
                    'title' => 'Edit Contact',
        )
        );
    }

    /**
     * Delete a Contact entity.
     *
     * @param int $id Contact id
     *
     * @return Response
     *
     * @Route("/{id}/delete", name="contact_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $flash = $this->get('braincrafted_bootstrap.flash');
        $contact = $em->getRepository('TruckeeProjectmanaBundle:Contact')->find($id);
        if (null === $contact) {
            $flash->alert('Contact does not exist');

            return $this->redirectToRoute('home');
        }
        $searches = $this->get('mana.searches');
        $disabledOptions = $searches->getDisabledOptions($contact);
        $form = $this->createForm(
            ContactType::class,
            $contact,
            ['disabledOptions' => $disabledOptions]
        );
        if ($request->isMethod('POST')) {
            $household = $contact->getHousehold();
            $hid = $household->getId();
            $household->removeContact($contact);
            $em->persist($household);
            $em->flush();
            $flash->alert('Contact has been deleted');

            return $this->redirectToRoute('contact_new', array('id' => $hid));
        }

        return $this->render(
            'Contact/delete.html.twig',
            array(
                    'contact' => $contact,
                    'form' => $form->createView(),
                    'title' => 'Delete Contact',
        )
        );
    }

    /**
     * Add contacts.
     *
     * Displays form to select site from which most recent contacts are
     * gathered.  Allows selecting and adding households. Includes setting
     * contact type.
     *
     * @param object $request Request
     * @param string $source Most recent/FY to date
     *
     * @Route("/addContacts/{source}", name="contacts_add")
     */
    public function addContactsAction(Request $request, $source)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $households = $request->request->get('contact_household');
            $data = $form->getData();
            $contactData['date'] = $data->getContactDate();
            $center = $data->getCenter();
            $contactData['center'] = $center;
            $contactData['desc'] = $data->getContactdesc();
            $desc = $contactData['desc']->getContactdesc();
            $centerName = $center->getCenter();
            $n = count($households);
            $flash = $this->get('braincrafted_bootstrap.flash');
            if ($n !== 0) {
                $em->getRepository('TruckeeProjectmanaBundle:Household')->addContacts(
                    $households,
                    $contactData
                );
                $flash->alert("$n $desc contacts added for $centerName");
            } else {
                $flash->alert('No contacts were added');

                return $this->redirectToRoute(
                    'contacts_add',
                    ['source' => $source]
                );
            }
        }

        return $this->render(
            'Contact/addContacts.html.twig',
            array(
                    'form' => $form->createView(),
                    'title' => 'Add contacts',
                    'source' => $source,
        )
        );
    }

    /**
     * Collect set of households
     *
     * @param string $site Site
     * @param string $source Most recent/FY to date
     *
     * @return string
     *
     * @Route("/latest/{site}/{source}")
     */
    public function mostRecentContactsAction($site, $source)
    {
        $em = $this->getDoctrine()->getManager();
        $center = $em->getRepository('TruckeeProjectmanaBundle:Center')->find($site);
        $searches = $this->get('mana.searches');
        if ('Most recent' === $source) {
            $contacts = $searches->getLatest($site);
        }
        if ('FY to date' === $source) {
            $fy = $this->fy();
            $contacts['contacts'] = $searches->getHeadsFYToDate($site, $fy);
            $contacts['latestDate'] = new \DateTime();
        }
        $content = $this->renderView(
            'Contact/mostRecentContacts.html.twig',
            [
                'contacts' => $contacts['contacts'],
                'latestDate' => $contacts['latestDate'],
                'site' => $center,
                'source' => $source,
        ]
        );
        $response = new Response($content);

        return $response;
    }

    /**
     * Generates PDF checklist of households at most recent distribution.
     *
     * @param object $request Request
     * @param string $source Most recent/FY to date
     *
     * @return file
     *
     * @Route("/latestReport/{source}", name="latest_contacts")
     */
    public function latestReportAction(
        Request $request,
        PdfService $pdf,
        $source
    ) {
        $center = new Center();
        $form = $this->createForm(SelectCenterType::class, $center);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //time limit extension required for multi-page rendering
            set_time_limit(0);
            $searches = $this->get('mana.searches');
            $id = $center->getCenter()->getId();
            $location = $center->getCenter()->getCenter();
            if ('Most recent' === $source) {
                $found = $searches->getLatest($id);
            }
            if ('FY to date' === $source) {
                $fy = $this->fy();
                $found['contacts'] = $searches->getHeadsFYToDate($id, $fy);
                $found['latestDate'] = date_format(new \DateTime(), 'm/d/Y');
            }
            if (count($found['contacts']) == 0 || empty($found)) {
                $flash = $this->get('braincrafted_bootstrap.flash');
                $flash->alert("No contacts found for $location");

                return $this->redirectToRoute(
                    'latest_contacts',
                    ['source' => $source]
                );
            }
            $date = new \DateTime($found['latestDate']);
            $filename = str_replace(' ', '', $source . $location) . date_format(
                $date,
                '_Ymd'
            ) . '.pdf';
            $header = $this->renderView(
                'Pdf/Contact/rosterHeader.html.twig',
                [
                    'date' => $found['latestDate'],
                    'center' => $location,
                    'source' => $source,]
            );
            $html = $this->renderView(
                'Pdf/Contact/rosterContent.html.twig',
                [
                    'date' => $found['latestDate'],
                    'center' => $location,
                    'source' => $source,
                    'contacts' => $found['contacts'],
            ]
            );

            $exec = $pdf->pdfExecutable();
            $snappy = new Pdf($exec);
            $snappy->setOption('header-html', $header);
            $snappy->setOption('footer-center', 'Page [page]');
            $content = $snappy->getOutputFromHtml($html);
            $response = new Response(
                $content,
                200,
                [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=' . urlencode($filename) . '.pdf',
            ]
            );

            return $response;
        }

        return $this->render(
            'Contact/latestReport.html.twig',
            array(
                    'title' => 'Select center',
                    'form' => $form->createView(),
                    'source' => $source,
        )
        );
    }
}
