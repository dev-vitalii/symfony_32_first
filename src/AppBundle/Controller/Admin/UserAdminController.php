<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserAdminController
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserAdminController extends Controller
{
    /**
     * @Route("/users", name="admin_users_list")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();
        return $this->render('admin/user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="admin_user_edit")
     */
    public function editUserAction(Request $request, User $user)
    {
        $form = $this->createForm('AppBundle\Form\EditUserFormType', $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user_data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $user_instance = $em->find('AppBundle:User', $request->get('id'));
            $user_instance->setRoles($user_data->getRoles());
            $user_instance->setEmail($user_data->getEmail());
            $em->flush();
            $this->addFlash('success', 'User updated!');
            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/user/edit.html.twig',[
            'user_edit_form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
