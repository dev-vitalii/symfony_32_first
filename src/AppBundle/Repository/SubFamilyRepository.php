<?php

namespace AppBundle\Repository;

/**
 * SubFamilyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubFamilyRepository extends \Doctrine\ORM\EntityRepository
{
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('sub_family')
            ->orderBy('sub_family.name', 'ASC');
    }


    public function findAny()
    {
        $minMax = $this->createQueryBuilder('sub_family')
            ->select('MIN(sub_family.id)')
            ->addSelect('MAX(sub_family.id)')
            ->getQuery()
            ->getSingleResult(null);
        ;
        $randomId = rand($minMax[1], $minMax[2]);

        $query = $this->createQueryBuilder('sub_family')
            ->where('sub_family.id > :rand')
            ->setParameter('rand', $randomId)
            ->orderBy('sub_family.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery();
        return $query->getSingleResult();
    }
}
