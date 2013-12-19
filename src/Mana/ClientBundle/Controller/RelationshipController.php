<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Relationship;
use Mana\ClientBundle\Form\RelationshipType;

/**
 * Relationship controller.
 *
 * @Route("/relation")
 */
class RelationshipController extends Controller {

    /**
     * Lists all Relationship entities.
     *
     * @Route("/", name="relation")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Relationship')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Relationship',
        );
    }

    /**
     * Creates a new Relationship entity.
     *
     * @Route("/", name="relation_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Relationship:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Relationship();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('relation_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Relationship',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Relationship',
        );
    }

    /**
     * Creates a form to create a Relationship entity.
     *
     * @param Relationship $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Relationship $entity) {
        $form = $this->createForm(new RelationshipType(), $entity, array(
            'action' => $this->generateUrl('relation_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Relationship entity.
     *
     * @Route("/new", name="relation_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Relationship();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Relationship',
        );
    }

    /**
     * Finds and displays a Relationship entity.
     *
     * @Route("/{id}", name="relation_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Relationship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relationship entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Relationship',
        );
    }

    /**
     * Displays a form to edit an existing Relationship entity.
     *
     * @Route("/{id}/edit", name="relation_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Relationship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relationship entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Relationship',
        );
    }

    /**
     * Creates a form to edit a Relationship entity.
     *
     * @param Relationship $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Relationship $entity) {
        $form = $this->createForm(new RelationshipType(), $entity, array(
            'action' => $this->generateUrl('relation_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Relationship entity.
     *
     * @Route("/{id}", name="relation_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Relationship:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Relationship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relationship entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('relation_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Relationship',
        );
    }

    /**
     * Deletes a Relationship entity.
     *
     * @Route("/{id}", name="relation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Relationship')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Relationship entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('relation'));
    }

    /**
     * Creates a form to delete a Relationship entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('relation_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
