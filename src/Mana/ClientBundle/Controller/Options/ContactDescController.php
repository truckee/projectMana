<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\ContactDesc;
use Mana\ClientBundle\Form\ContactDescType;

/**
 * ContactDesc controller.
 *
 * @Route("/desc")
 */
class ContactDescController extends Controller {

    /**
     * Lists all ContactDesc entities.
     *
     * @Route("/", name="desc")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:ContactDesc')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Contact descriptions',
        );
    }

    /**
     * Creates a new ContactDesc entity.
     *
     * @Route("/", name="desc_create")
     * @Method("POST")
     * @Template("ManaClientBundle:ContactDesc:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ContactDesc();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('desc_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Contact descriptions',)));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Contact descriptions',
        );
    }

    /**
     * Creates a form to create a ContactDesc entity.
     *
     * @param ContactDesc $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContactDesc $entity) {
        $form = $this->createForm(new ContactDescType(), $entity, array(
            'action' => $this->generateUrl('desc_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ContactDesc entity.
     *
     * @Route("/new", name="desc_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ContactDesc();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Contact descriptions',
        );
    }

    /**
     * Finds and displays a ContactDesc entity.
     *
     * @Route("/{id}", name="desc_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:ContactDesc')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContactDesc entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Contact descriptions',
        );
    }

    /**
     * Displays a form to edit an existing ContactDesc entity.
     *
     * @Route("/{id}/edit", name="desc_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:ContactDesc')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContactDesc entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Contact descriptions',
        );
    }

    /**
     * Creates a form to edit a ContactDesc entity.
     *
     * @param ContactDesc $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ContactDesc $entity) {
        $form = $this->createForm(new ContactDescType(), $entity, array(
            'action' => $this->generateUrl('desc_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing ContactDesc entity.
     *
     * @Route("/{id}", name="desc_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:ContactDesc:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:ContactDesc')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContactDesc entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('desc_edit', array('id' => $id,
                                'title' => 'Contact descriptions',)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Contact descriptions',
        );
    }

    /**
     * Deletes a ContactDesc entity.
     *
     * @Route("/{id}", name="desc_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:ContactDesc')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ContactDesc entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('desc'));
    }

    /**
     * Creates a form to delete a ContactDesc entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('desc_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
