<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Reason;
use Mana\ClientBundle\Form\ReasonType;

/**
 * Reason controller.
 *
 * @Route("/reason")
 */
class ReasonController extends Controller {

    /**
     * Lists all Reason entities.
     *
     * @Route("/", name="reason")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Reason')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Insufficient food',
        );
    }

    /**
     * Creates a new Reason entity.
     *
     * @Route("/", name="reason_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Reason:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Reason();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('reason_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Insufficient food',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Insufficient food',
        );
    }

    /**
     * Creates a form to create a Reason entity.
     *
     * @param Reason $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Reason $entity) {
        $form = $this->createForm(new ReasonType(), $entity, array(
            'action' => $this->generateUrl('reason_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Reason entity.
     *
     * @Route("/new", name="reason_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Reason();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Reason entity.
     *
     * @Route("/{id}", name="reason_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Reason')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reason entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Insufficient food',
        );
    }

    /**
     * Displays a form to edit an existing Reason entity.
     *
     * @Route("/{id}/edit", name="reason_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Reason')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reason entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Insufficient food',
        );
    }

    /**
     * Creates a form to edit a Reason entity.
     *
     * @param Reason $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Reason $entity) {
        $form = $this->createForm(new ReasonType(), $entity, array(
            'action' => $this->generateUrl('reason_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Reason entity.
     *
     * @Route("/{id}", name="reason_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Reason:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Reason')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reason entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('reason_edit', array(
                                'id' => $id,
                                'title' => 'Insufficient food',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Insufficient food',
        );
    }

    /**
     * Deletes a Reason entity.
     *
     * @Route("/{id}", name="reason_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Reason')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Reason entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('reason'));
    }

    /**
     * Creates a form to delete a Reason entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('reason_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
