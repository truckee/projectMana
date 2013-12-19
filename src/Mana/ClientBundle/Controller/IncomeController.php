<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Income;
use Mana\ClientBundle\Form\IncomeType;

/**
 * Income controller.
 *
 * @Route("/income")
 */
class IncomeController extends Controller {

    /**
     * Lists all Income entities.
     *
     * @Route("/", name="income")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Income')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Income',
        );
    }

    /**
     * Creates a new Income entity.
     *
     * @Route("/", name="income_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Income:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Income();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('income_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Income',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Income',
        );
    }

    /**
     * Creates a form to create a Income entity.
     *
     * @param Income $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Income $entity) {
        $form = $this->createForm(new IncomeType(), $entity, array(
            'action' => $this->generateUrl('income_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Income entity.
     *
     * @Route("/new", name="income_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Income();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Income',
        );
    }

    /**
     * Finds and displays a Income entity.
     *
     * @Route("/{id}", name="income_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Income')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Income entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Income',
        );
    }

    /**
     * Displays a form to edit an existing Income entity.
     *
     * @Route("/{id}/edit", name="income_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Income')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Income entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Income',
        );
    }

    /**
     * Creates a form to edit a Income entity.
     *
     * @param Income $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Income $entity) {
        $form = $this->createForm(new IncomeType(), $entity, array(
            'action' => $this->generateUrl('income_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Income entity.
     *
     * @Route("/{id}", name="income_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Income:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Income')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Income entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('income_edit', array(
                                'id' => $id,
                                'title' => 'Income',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Income',
        );
    }

    /**
     * Deletes a Income entity.
     *
     * @Route("/{id}", name="income_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Income')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Income entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('income'));
    }

    /**
     * Creates a form to delete a Income entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('income_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
