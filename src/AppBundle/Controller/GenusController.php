<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use AppBundle\Entity\GenusScientist;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenusController extends Controller
{
    /**
     * @Route("/genus/new")
    */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genus = new Genus();
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(['email' => 'aquanaut1@example.org']);
        //$genus->addGenusScientist($user);

        $genus->setName('Octopus'.rand(1,100));
        $genus->setSpeciesCount(rand(1,10));
        $subFamily = $em->getRepository('AppBundle:SubFamily')
            ->findAny();
        $genus->setSubFamily($subFamily);
        $genus->setFunFact( 'Fun fact))' );

        $note = new GenusNote();
        $note->setUsername('AquaWeaver');
        $note->setUserAvatarFilename('ryan.jpeg');
        $note->setNote('I counted 8 legs... as they wrapped around me');
        $note->setCreatedAt(new \DateTime('-1 month'));
        $note->setGenus($genus);

        $genusScientist = new GenusScientist();
        $genusScientist->setGenus($genus);
        $genusScientist->setUser($user);
        $genusScientist->setYearsStudied(10);


        $em->persist($genusScientist);
        $em->persist($genus);
        $em->persist($note);
        $em->flush();

        return new Response(sprintf(
            '<html><body>Genus created! <a href="%s">%s</a></body></html>',
            $this->generateUrl('genus_show', ['slug' => $genus->getSlug()]),
            $genus->getName()
        ));
    }

    /**
     * @Route("/genus")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $genuses = $em->getRepository('AppBundle:Genus')
            ->findAllPublishedOrderedByRecentlyActive();

        return $this->render('/genus/list.html.twig', [
            'genuses' => $genuses,
        ]);
    }

    /**
     * @Route("/genus/{slug}", name="genus_show")
     */
    public function showAction(Genus $genus)
    {

        $em = $this->getDoctrine()->getManager();

        if(!$genus) {
            throw $this->createNotFoundException('Genus not found');
        }

        $recentNotes = $em->getRepository('AppBundle:GenusNote')
            ->findAllRecentNotesForGenus($genus);

        return $this->render('genus/show.html.twig', array(
            'genus' => $genus,
            'recentNoteCount' => count($recentNotes),
        ));
    }

    /**
     * @Route("/genus/{slug}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus)
    {

        $notes = [];
        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }

        $data = [
            'notes' => $notes,
        ];

        return new JsonResponse( $data );
    }

    /**
     * @Route("/genus/{genusId}/scientists/{userId}", name="genus_scientists_remove")
     * @Method("DELETE")
     */
    public function removeGenusScientistAction($genusId, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $genusScientist = $em->getRepository('AppBundle:GenusScientist')
            ->findOneBy([
                'user' => $userId,
                'genus' => $genusId
            ]);
        $em->remove($genusScientist);
        $em->flush();

        return new Response(null, 204);
    }
}