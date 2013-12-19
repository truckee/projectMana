<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Ethnicity;
use Mana\ClientBundle\Form\EthnicityType;

/**
 * Ethnicity controller.
 *
 * @Route("/eth")
 */
class EthnicityController extends Controller {

    /**
     * Lists all Ethnicity entities.
     *
     * @Route("/", name="eth")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Ethnicity')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Ethnicity',
        );
    }

    /**
     * Creates a new Ethnicity entity.
     *
     * @Route("/", name="eth_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Ethnicity:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Ethnicity();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('eth_show', array('id' => $entity->getId(),
                                'title' => 'Ethnicity',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Ethnicity',
        );
    }

    /**
     * Creates a form to create a Ethnicity entity.
     *
     * @param Ethnicity $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ethnicity $entity) {
        $form = $this->createForm(new EthnicityType(), $entity, array(
            'action' => $this->generateUrl('eth_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Ethnicity entity.
     *
     * @Route("/new", name="eth_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Ethnicity();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Ethnicity',
        );
    }

    /**
     * Finds and displays a Ethnicity entity.
     *
     * @Route("/{id}", name="eth_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Ethnicity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ethnicity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Ethnicity',
        );
    }

    /**
     * Displays a form to edit an existing Ethnicity entity.
     *
     * @Route("/{id}/edit", name="eth_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Ethnicity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ethnicity entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Ethnicity',
        );
    }

    /**
     * Creates a form to edit a Ethnicity entity.
     *
     * @param Ethnicity $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Ethnicity $entity) {
        $form = $this->createForm(new EthnicityType(), $entity, array(
            'action' => $this->generateUrl('eth_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Ethnicity entity.
     *
     * @Route("/{id}", name="eth_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Ethnicity:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Ethnicity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ethnicity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('eth_edit', array(
                                'id' => $id,
                                'title' => 'Ethnicity',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Ethnicity entity.
     *
     * @Route("/{id}", name="eth_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Ethnicity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ethnicity entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('eth'));
    }

    /**
     * Creates a form to delete a Ethnicity entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('eth_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
