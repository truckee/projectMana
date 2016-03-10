<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Appliance;
use Mana\ClientBundle\Form\ApplianceType;

/**
 * Appliance controller.
 *
 * @Route("/appliance")
 */
class ApplianceController extends Controller {

    /**
     * Lists all Appliance entities.
     *
     * @Route("/", name="appliance")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Appliance')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Appliances',
        );
    }

    /**
     * Creates a new Appliance entity.
     *
     * @Route("/", name="appliance_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Appliance:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Appliance();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('appliance_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Appliances'
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Appliances',
        );
    }

    /**
     * Creates a form to create a Appliance entity.
     *
     * @param Appliance $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Appliance $entity) {
        $form = $this->createForm(new ApplianceType(), $entity, array(
            'action' => $this->generateUrl('appliance_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Appliance entity.
     *
     * @Route("/new", name="appliance_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Appliance();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Appliances',
        );
    }

    /**
     * Finds and displays a Appliance entity.
     *
     * @Route("/{id}", name="appliance_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Appliance')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Appliance entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Appliances',
        );
    }

    /**
     * Displays a form to edit an existing Appliance entity.
     *
     * @Route("/{id}/edit", name="appliance_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Appliance')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Appliance entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Appliances',
        );
    }

    /**
     * Creates a form to edit a Appliance entity.
     *
     * @param Appliance $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Appliance $entity) {
        $form = $this->createForm(new ApplianceType(), $entity, array(
            'action' => $this->generateUrl('appliance_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Appliance entity.
     *
     * @Route("/{id}", name="appliance_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Appliance:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Appliance')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Appliance entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('appliance_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Appliances',
        );
    }

    /**
     * Deletes a Appliance entity.
     *
     * @Route("/{id}", name="appliance_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Appliance')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Appliance entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('appliance', array(
                            'title' => 'Appliances',
        )));
    }

    /**
     * Creates a form to delete a Appliance entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('appliance_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
