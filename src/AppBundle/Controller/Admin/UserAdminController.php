<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserAdminController
 * @Route("/admin")
 */
class UserAdminController extends Controller
{
    /**
     * @Route("/users", name="users_list")
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
    public function editUserAction(User $user)
    {
        dump($user);
        die();
    }
}
