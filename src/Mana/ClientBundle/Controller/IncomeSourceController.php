<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\IncomeSource;
use Mana\ClientBundle\Form\IncomeSourceType;

/**
 * IncomeSource controller.
 *
 * @Route("/incsource")
 */
class IncomeSourceController extends Controller {

    /**
     * Lists all IncomeSource entities.
     *
     * @Route("/", name="incsource")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:IncomeSource')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Income source',
        );
    }

    /**
     * Creates a new IncomeSource entity.
     *
     * @Route("/", name="incsource_create")
     * @Method("POST")
     * @Template("ManaClientBundle:IncomeSource:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new IncomeSource();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('incsource_show', array(
                                'id' => $entity->getId(),
                                'title' => 'Income source',
            )));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Income source',
        );
    }

    /**
     * Creates a form to create a IncomeSource entity.
     *
     * @param IncomeSource $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(IncomeSource $entity) {
        $form = $this->createForm(new IncomeSourceType(), $entity, array(
            'action' => $this->generateUrl('incsource_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new IncomeSource entity.
     *
     * @Route("/new", name="incsource_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new IncomeSource();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'title' => 'Income source',
        );
    }

    /**
     * Finds and displays a IncomeSource entity.
     *
     * @Route("/{id}", name="incsource_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:IncomeSource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IncomeSource entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Income source',
        );
    }

    /**
     * Displays a form to edit an existing IncomeSource entity.
     *
     * @Route("/{id}/edit", name="incsource_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:IncomeSource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IncomeSource entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Income source',
        );
    }

    /**
     * Creates a form to edit a IncomeSource entity.
     *
     * @param IncomeSource $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(IncomeSource $entity) {
        $form = $this->createForm(new IncomeSourceType(), $entity, array(
            'action' => $this->generateUrl('incsource_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing IncomeSource entity.
     *
     * @Route("/{id}", name="incsource_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:IncomeSource:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:IncomeSource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IncomeSource entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('incsource_edit', array(
                                'id' => $id,
                                'title' => 'Income source',
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Income source',
        );
    }

    /**
     * Deletes a IncomeSource entity.
     *
     * @Route("/{id}", name="incsource_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:IncomeSource')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find IncomeSource entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('incsource'));
    }

    /**
     * Creates a form to delete a IncomeSource entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('incsource_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
