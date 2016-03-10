<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Housing;
use Mana\ClientBundle\Form\HousingType;

/**
 * Housing controller.
 *
 * @Route("/housing")
 */
class HousingController extends Controller {

    /**
     * Lists all Housing entities.
     *
     * @Route("/", name="housing")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Housing')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Housing',
        );
    }

    /**
     * Creates a new Housing entity.
     *
     * @Route("/", name="housing_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Housing:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Housing();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('housing_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Housing',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Housing',
        );
    }

    /**
     * Creates a form to create a Housing entity.
     *
     * @param Housing $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Housing $entity) {
        $form = $this->createForm(new HousingType(), $entity, array(
            'action' => $this->generateUrl('housing_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Housing entity.
     *
     * @Route("/new", name="housing_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Housing();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Housing',
        );
    }

    /**
     * Finds and displays a Housing entity.
     *
     * @Route("/{id}", name="housing_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Housing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Housing entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Housing',
        );
    }

    /**
     * Displays a form to edit an existing Housing entity.
     *
     * @Route("/{id}/edit", name="housing_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Housing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Housing entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Housing',
        );
    }

    /**
     * Creates a form to edit a Housing entity.
     *
     * @param Housing $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Housing $entity) {
        $form = $this->createForm(new HousingType(), $entity, array(
            'action' => $this->generateUrl('housing_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Housing entity.
     *
     * @Route("/{id}", name="housing_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Housing:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Housing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Housing entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('housing_edit', array(
                                'id' => $id,
                                'title' => 'Housing',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Housing',
        );
    }

    /**
     * Deletes a Housing entity.
     *
     * @Route("/{id}", name="housing_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Housing')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Housing entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('housing'));
    }

    /**
     * Creates a form to delete a Housing entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('housing_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
