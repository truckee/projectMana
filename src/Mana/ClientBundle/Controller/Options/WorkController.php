<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Work;
use Mana\ClientBundle\Form\WorkType;

/**
 * Work controller.
 *
 * @Route("/work")
 */
class WorkController extends Controller {

    /**
     * Lists all Work entities.
     *
     * @Route("/", name="work")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Work')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Work',
        );
    }

    /**
     * Creates a new Work entity.
     *
     * @Route("/", name="work_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Work:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Work();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('work_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Work',
        );
    }

    /**
     * Creates a form to create a Work entity.
     *
     * @param Work $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Work $entity) {
        $form = $this->createForm(new WorkType(), $entity, array(
            'action' => $this->generateUrl('work_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Work entity.
     *
     * @Route("/new", name="work_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Work();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Work',
        );
    }

    /**
     * Finds and displays a Work entity.
     *
     * @Route("/{id}", name="work_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Work')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Work entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Work',
        );
    }

    /**
     * Displays a form to edit an existing Work entity.
     *
     * @Route("/{id}/edit", name="work_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Work')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Work entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Work',
        );
    }

    /**
     * Creates a form to edit a Work entity.
     *
     * @param Work $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Work $entity) {
        $form = $this->createForm(new WorkType(), $entity, array(
            'action' => $this->generateUrl('work_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Work entity.
     *
     * @Route("/{id}", name="work_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Work:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Work')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Work entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('work_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Work entity.
     *
     * @Route("/{id}", name="work_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Work')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Work entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('work'));
    }

    /**
     * Creates a form to delete a Work entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('work_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
