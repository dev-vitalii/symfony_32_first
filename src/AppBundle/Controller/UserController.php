<?php


namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use AppBundle\Security\LoginFormAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request, LoginFormAuthenticator $authenticator)
    {
        $form = $this->createForm(UserRegistrationForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Welcome '.$user->getEmail());
            //return $this->redirectToRoute('homepage');
            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main'
                );
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}", name="user_show")
     */
    public function showAction(User $user)
    {
        return $this->render('user/show.html.twig', array(
            'user' => $user
        ));
    }

}