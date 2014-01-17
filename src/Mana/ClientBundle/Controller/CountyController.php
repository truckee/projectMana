<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\County;
use Mana\ClientBundle\Form\CountyType;

/**
 * County controller.
 *
 * @Route("/county")
 */
class CountyController extends Controller {

    /**
     * Lists all County entities.
     *
     * @Route("/", name="county")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:County')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Counties',
        );
    }

    /**
     * Creates a new County entity.
     *
     * @Route("/", name="county_create")
     * @Method("POST")
     * @Template("ManaClientBundle:County:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new County();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('county_show', array('id' => $entity->getId(),
                                'title' => 'Counties',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Counties',
        );
    }

    /**
     * Creates a form to create a County entity.
     *
     * @param County $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(County $entity) {
        $form = $this->createForm(new CountyType(), $entity, array(
            'action' => $this->generateUrl('county_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new County entity.
     *
     * @Route("/new", name="county_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new County();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Counties',
        );
    }

    /**
     * Finds and displays a County entity.
     *
     * @Route("/{id}", name="county_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:County')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find County entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Counties',
        );
    }

    /**
     * Displays a form to edit an existing County entity.
     *
     * @Route("/{id}/edit", name="county_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:County')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find County entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Counties',
        );
    }

    /**
     * Creates a form to edit a County entity.
     *
     * @param County $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(County $entity) {
        $form = $this->createForm(new CountyType(), $entity, array(
            'action' => $this->generateUrl('county_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing County entity.
     *
     * @Route("/{id}", name="county_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:County:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:County')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find County entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('county_edit', array('id' => $id,
                                'title' => 'Counties',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Counties',
        );
    }

    /**
     * Deletes a County entity.
     *
     * @Route("/{id}", name="county_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:County')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find County entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('county', array(
                            'title' => 'Counties',)));
    }

    /**
     * Creates a form to delete a County entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('county_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
