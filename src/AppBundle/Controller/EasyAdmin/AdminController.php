<?php


namespace AppBundle\Controller\EasyAdmin;


use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController
{
    public function changePublishedStatusAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id = $this->request->query->get('id');
        $entity = $em->getRepository('AppBundle:Genus')->find($id);
        $entity->setIsPublished(!$entity->getIsPublished());
        $em->flush();
        $this->addFlash('success', sprintf('Genus %spublished!', $entity->getIsPublished() ? '' : 'un'));
        return $this->redirectToRoute('easyadmin', [
            'action' => 'show',
            'entity' => $this->request->query->get('entity'),
            'id' => $id,
        ]);
    }
}