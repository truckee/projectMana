<?php

namespace Mana\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mana\ClientBundle\Entity\User;
use Mana\ClientBundle\Form\UserCreateType;
use Mana\ClientBundle\Form\UserEditType;
use Mana\ClientBundle\Form\UserPasswordType;

/**
 * Class UserController
 * @Route("/user") 
 */
class UserController extends Controller {

    /**
     * entry to user actions
     * @Route("/", name="user_main")
     * @Template() 
     */
    public function mainAction() {
        $user = new User();
        $user->setIsActive(1);
        $user->setRole('ROLE_USER');
        $form = $this->createForm(new UserCreateType(), $user);
        $em = $this->getDoctrine()->getManager();
        $sql = "SELECT u FROM ManaClientBundle:User u WHERE u.role <> 'ROLE_SUPER_ADMIN'";
        $users = $em->createQuery($sql)->getResult();

        return array(
            'form' => $form->createView(),
            'user' => $user,
            'users' => $users,
            'title' => 'Manage users',
            'menu' => 'admin',
        );
    }

    /**
     * Create new user
     * @param Request $request 
     * @Route("/create", name="user_create")
     * @Method("POST")
     * @Template("ManaClientBundle:User:main.html.twig")
     */
    public function createAction(Request $request) {
        $user = new User();
        $form = $this->createForm(new UserCreateType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('user_main', array()));
        }
        return array(
            'form' => $form->createView(),
            'user' => $user,
            'title' => 'Add user',
            'menu' => 'admin',
        );
    }

    /**
     *  Edit a user
     * @param type $id 
     * @Route("/{id}/edit", name="user_edit")
     * @Template("ManaClientBundle:User:edit.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ManaClientBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $form = $this->createForm(new UserEditType(), $user);
        return array(
            'form' => $form->createView(),
            'user' => $user,
            'title' => 'Edit user',
            'menu' => 'admin',
        );
    }

    /**
     *  Update a user
     * @param type $id 
     * @Route("/{id}/update", name="user_update")
     * @Template("ManaClientBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ManaClientBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $form = $this->createForm(new UserEditType(), $user);
        $form->handlerequest($request);
        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('user_main', array()));
        }
        return array(
            'form' => $form->createView(),
            'user' => $user,
            'title' => 'Edit user',
            'menu' => 'admin',
        );
    }

    /**
     *  Change a user's password
     * @param type $id 
     * @Route("/{id}/pwd", name="pwd_edit")
     * @Template("ManaClientBundle:User:password.html.twig")
     */
    public function pwdEditAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ManaClientBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $form = $this->createForm(new UserPasswordType(), $user);
        return array(
            'form' => $form->createView(),
            'user' => $user,
            'title' => 'Edit user password',
            'menu' => 'admin',
        );
    }

    /**
     *  Update a user's password
     * @param type $id 
     * @Route("/{id}/pwdupdate", name="pwd_update")
     * @Template("ManaClientBundle:User:password.html.twig")
     */
    public function pwdUpdateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ManaClientBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $form = $this->createForm(new UserPasswordType(), $user);
        $form->handlerequest($request);
        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('user_main', array()));
        }
        return array(
            'form' => $form->createView(),
            'user' => $user,
            'title' => 'Edit user password',
            'menu' => 'admin',
        );
    }

    /**
     * Deletes a User entity.
     * @Route("/{id}/delete", name="user_delete")
     * @Template("ManaClientBundle:User:user_delete.html.twig")
     */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ManaClientBundle:User')->find($id);
        $form = $this->createForm(new UserEditType(), $user);
        if ($request->isMethod('POST')) {
            $em->remove($user);
            $em->flush();
            return $this->redirect($this->generateUrl('user_main'));
        }
        return array(
            'user' => $user,
            'form' => $form->createView(),
            'title' => 'Delete User',
            'menu' => 'admin',
        );
    }

}