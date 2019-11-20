<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    /**
     * @return Genus[]
     */
    public function findAllPublishedOrderedByRecentlyActive()
    {
        return $this->createQueryBuilder('genus')
            ->andWhere('genus.isPublished = :isPublished')
            ->setParameter('isPublished', 1)
            ->leftJoin('genus.notes', 'genus_note')
            ->leftJoin('genus.genusScientists', 'genusScientists')
            ->addSelect('genusScientists')
            ->orderBy('genus_note.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}