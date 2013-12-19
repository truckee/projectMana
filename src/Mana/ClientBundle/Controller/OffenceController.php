<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Offence;
use Mana\ClientBundle\Form\OffenceType;

/**
 * Offence controller.
 *
 * @Route("/offence")
 */
class OffenceController extends Controller {

    /**
     * Lists all Offence entities.
     *
     * @Route("/", name="offence")
     * @Method("GET")
     * @Template()
     */
    public function listAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Offence')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Offence',
        );
    }

    /**
     * Creates a new Offence entity.
     *
     * @Route("/", name="offence_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Offence:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Offence();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('offence_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Offence',
        );
    }

    /**
     * Creates a form to create a Offence entity.
     *
     * @param Offence $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Offence $entity) {
        $form = $this->createForm(new OffenceType(), $entity, array(
            'action' => $this->generateUrl('offence_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Offence entity.
     *
     * @Route("/new", name="offence_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Offence();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Offence',
        );
    }

    /**
     * Finds and displays a Offence entity.
     *
     * @Route("/{id}", name="offence_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Offence')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Offence entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Offence',
        );
    }

    /**
     * Displays a form to edit an existing Offence entity.
     *
     * @Route("/{id}/edit", name="offence_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Offence')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Offence entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Offence',
        );
    }

    /**
     * Creates a form to edit a Offence entity.
     *
     * @param Offence $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Offence $entity) {
        $form = $this->createForm(new OffenceType(), $entity, array(
            'action' => $this->generateUrl('offence_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Offence entity.
     *
     * @Route("/{id}", name="offence_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Offence:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Offence')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Offence entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('offence_edit', array(
                                'id' => $id,
                                'title' => 'Offence',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Offence',
        );
    }

    /**
     * Deletes a Offence entity.
     *
     * @Route("/{id}", name="offence_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Offence')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Offence entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('offence'));
    }

    /**
     * Creates a form to delete a Offence entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('offence_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
