<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Center;
use Mana\ClientBundle\Form\CenterType;

/**
 * Center controller.
 *
 * @Route("/center")
 */
class CenterController extends Controller
{

    /**
     * Lists all Center entities.
     *
     * @Route("/", name="center")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Center')->findAll();

        return array(
            'entities' => $entities,
            'title' => 'Centers',
        );
    }
    /**
     * Creates a new Center entity.
     *
     * @Route("/", name="center_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Center:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Center();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('center_show', array(
                'id' => $entity->getId(),
            'title' => 'Centers',)));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'title' => 'Centers',
        );
    }

    /**
    * Creates a form to create a Center entity.
    *
    * @param Center $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Center $entity)
    {
        $form = $this->createForm(new CenterType(), $entity, array(
            'action' => $this->generateUrl('center_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Center entity.
     *
     * @Route("/new", name="center_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Center();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'title' => 'Centers',
        );
    }

    /**
     * Finds and displays a Center entity.
     *
     * @Route("/{id}", name="center_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Center')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Center entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Centers',
        );
    }

    /**
     * Displays a form to edit an existing Center entity.
     *
     * @Route("/{id}/edit", name="center_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Center')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Center entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Centers',
        );
    }

    /**
    * Creates a form to edit a Center entity.
    *
    * @param Center $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Center $entity)
    {
        $form = $this->createForm(new CenterType(), $entity, array(
            'action' => $this->generateUrl('center_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Center entity.
     *
     * @Route("/{id}", name="center_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Center:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Center')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Center entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('center_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Centers',
        );
    }
    /**
     * Deletes a Center entity.
     *
     * @Route("/{id}", name="center_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Center')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Center entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('center'));
    }

    /**
     * Creates a form to delete a Center entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('center_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
