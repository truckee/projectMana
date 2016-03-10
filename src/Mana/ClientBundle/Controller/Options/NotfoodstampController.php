<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Notfoodstamp;
use Mana\ClientBundle\Form\NotfoodstampType;

/**
 * Notfoodstamp controller.
 *
 * @Route("/notfoodstamp")
 */
class NotfoodstampController extends Controller {

    /**
     * Lists all Notfoodstamp entities.
     *
     * @Route("/", name="notfoodstamp")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Notfoodstamp')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Not food stamps?',
        );
    }

    /**
     * Creates a new Notfoodstamp entity.
     *
     * @Route("/", name="notfoodstamp_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Notfoodstamp:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Notfoodstamp();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('notfoodstamp_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Not food stamps?',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Not food stamps?',
        );
    }

    /**
     * Creates a form to create a Notfoodstamp entity.
     *
     * @param Notfoodstamp $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Notfoodstamp $entity) {
        $form = $this->createForm(new NotfoodstampType(), $entity, array(
            'action' => $this->generateUrl('notfoodstamp_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Notfoodstamp entity.
     *
     * @Route("/new", name="notfoodstamp_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Notfoodstamp();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Not food stamps?',
        );
    }

    /**
     * Finds and displays a Notfoodstamp entity.
     *
     * @Route("/{id}", name="notfoodstamp_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Notfoodstamp')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notfoodstamp entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Not food stamps?',
        );
    }

    /**
     * Displays a form to edit an existing Notfoodstamp entity.
     *
     * @Route("/{id}/edit", name="notfoodstamp_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Notfoodstamp')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notfoodstamp entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Not food stamps?',
        );
    }

    /**
     * Creates a form to edit a Notfoodstamp entity.
     *
     * @param Notfoodstamp $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Notfoodstamp $entity) {
        $form = $this->createForm(new NotfoodstampType(), $entity, array(
            'action' => $this->generateUrl('notfoodstamp_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Notfoodstamp entity.
     *
     * @Route("/{id}", name="notfoodstamp_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Notfoodstamp:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Notfoodstamp')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notfoodstamp entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('notfoodstamp_edit', array(
                                'id' => $id,
                                'title' => 'Not food stamps?',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Not food stamps?',
        );
    }

    /**
     * Deletes a Notfoodstamp entity.
     *
     * @Route("/{id}", name="notfoodstamp_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Notfoodstamp')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Notfoodstamp entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('notfoodstamp'));
    }

    /**
     * Creates a form to delete a Notfoodstamp entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('notfoodstamp_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
