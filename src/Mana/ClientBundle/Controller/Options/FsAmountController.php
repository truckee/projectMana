<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\FsAmount;
use Mana\ClientBundle\Form\FsAmountType;

/**
 * FsAmount controller.
 *
 * @Route("/fsamount")
 */
class FsAmountController extends Controller {

    /**
     * Lists all FsAmount entities.
     *
     * @Route("/", name="fsamount")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:FsAmount')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Food stamp amt',
        );
    }

    /**
     * Creates a new FsAmount entity.
     *
     * @Route("/", name="fsamount_create")
     * @Method("POST")
     * @Template("ManaClientBundle:FsAmount:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new FsAmount();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fsamount_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Food stamp amt',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Food stamp amt',
        );
    }

    /**
     * Creates a form to create a FsAmount entity.
     *
     * @param FsAmount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FsAmount $entity) {
        $form = $this->createForm(new FsAmountType(), $entity, array(
            'action' => $this->generateUrl('fsamount_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new FsAmount entity.
     *
     * @Route("/new", name="fsamount_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new FsAmount();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Food stamp amt',
        );
    }

    /**
     * Finds and displays a FsAmount entity.
     *
     * @Route("/{id}", name="fsamount_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:FsAmount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FsAmount entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Food stamp amt',
        );
    }

    /**
     * Displays a form to edit an existing FsAmount entity.
     *
     * @Route("/{id}/edit", name="fsamount_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:FsAmount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FsAmount entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Food stamp amt',
        );
    }

    /**
     * Creates a form to edit a FsAmount entity.
     *
     * @param FsAmount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FsAmount $entity) {
        $form = $this->createForm(new FsAmountType(), $entity, array(
            'action' => $this->generateUrl('fsamount_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing FsAmount entity.
     *
     * @Route("/{id}", name="fsamount_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:FsAmount:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:FsAmount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FsAmount entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('fsamount_edit', array(
                                'id' => $id,
                                'title' => 'Food stamp amt',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a FsAmount entity.
     *
     * @Route("/{id}", name="fsamount_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:FsAmount')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FsAmount entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('fsamount'));
    }

    /**
     * Creates a form to delete a FsAmount entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('fsamount_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
