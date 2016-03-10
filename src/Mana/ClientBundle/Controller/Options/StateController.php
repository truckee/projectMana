<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\State;
use Mana\ClientBundle\Form\StateType;

/**
 * State controller.
 *
 * @Route("/state")
 */
class StateController extends Controller {

    /**
     * Lists all State entities.
     *
     * @Route("/", name="state")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:State')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'State',
        );
    }

    /**
     * Creates a new State entity.
     *
     * @Route("/", name="state_create")
     * @Method("POST")
     * @Template("ManaClientBundle:State:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new State();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('state_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'State',
        );
    }

    /**
     * Creates a form to create a State entity.
     *
     * @param State $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(State $entity) {
        $form = $this->createForm(new StateType(), $entity, array(
            'action' => $this->generateUrl('state_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new State entity.
     *
     * @Route("/new", name="state_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new State();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'State',
        );
    }

    /**
     * Finds and displays a State entity.
     *
     * @Route("/{id}", name="state_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:State')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find State entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'State',
        );
    }

    /**
     * Displays a form to edit an existing State entity.
     *
     * @Route("/{id}/edit", name="state_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:State')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find State entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'State',
        );
    }

    /**
     * Creates a form to edit a State entity.
     *
     * @param State $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(State $entity) {
        $form = $this->createForm(new StateType(), $entity, array(
            'action' => $this->generateUrl('state_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing State entity.
     *
     * @Route("/{id}", name="state_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:State:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:State')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find State entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('state_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a State entity.
     *
     * @Route("/{id}", name="state_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:State')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find State entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('state'));
    }

    /**
     * Creates a form to delete a State entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('state_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
