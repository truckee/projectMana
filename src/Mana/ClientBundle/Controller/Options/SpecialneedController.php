<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\Specialneed;
use Mana\ClientBundle\Form\SpecialneedType;

/**
 * Specialneed controller.
 * Unused; remnant of abandoned update request
 *
 * @Route("/specialneed")
 */
class SpecialneedController extends Controller
{

    /**
     * Lists all Specialneed entities.
     *
     * @Route("/", name="specialneed")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ManaClientBundle:Specialneed')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Specialneed entity.
     *
     * @Route("/", name="specialneed_create")
     * @Method("POST")
     * @Template("ManaClientBundle:Specialneed:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Specialneed();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('specialneed_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Specialneed entity.
    *
    * @param Specialneed $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Specialneed $entity)
    {
        $form = $this->createForm(new SpecialneedType(), $entity, array(
            'action' => $this->generateUrl('specialneed_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Specialneed entity.
     *
     * @Route("/new", name="specialneed_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Specialneed();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Specialneed entity.
     *
     * @Route("/{id}", name="specialneed_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Specialneed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialneed entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Specialneed entity.
     *
     * @Route("/{id}/edit", name="specialneed_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Specialneed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialneed entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Specialneed entity.
    *
    * @param Specialneed $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Specialneed $entity)
    {
        $form = $this->createForm(new SpecialneedType(), $entity, array(
            'action' => $this->generateUrl('specialneed_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Specialneed entity.
     *
     * @Route("/{id}", name="specialneed_update")
     * @Method("PUT")
     * @Template("ManaClientBundle:Specialneed:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ManaClientBundle:Specialneed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialneed entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('specialneed_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Specialneed entity.
     *
     * @Route("/{id}", name="specialneed_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ManaClientBundle:Specialneed')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Specialneed entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('specialneed'));
    }

    /**
     * Creates a form to delete a Specialneed entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('specialneed_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
