<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    // Fonction permmettant de chercher dans la BDD en fonction d'un terme
    public function search(string $filter)
    {
        $builder = $this->createQueryBuilder('art');

        $builder
            ->andWhere('art.nom LIKE :nom')
            ->setParameter('nom', '%'. $filter . '%')
        ;

        $query = $builder->getQuery();

        return $query->getResult();

    }

    // méthode liée à l'autocomplétion de la barre de recherche

    public function autocomplete($term)
    {
        $qb = $this->createQueryBuilder('articles');

        $qb->select('articles.nom')
            ->where('articles.nom LIKE :term')
            ->setParameter('term', '%' . $term . '%');

        $arrayAss = $qb->getQuery()
            ->getResult();

        $array = array();

        // le résultat de la requête est bouclé afin d'effectuer la recherche sur chaque ligne de la table article
        foreach ($arrayAss as $data) {

            $array[] = $data['nom'];
        }

        return $array;
    }


    // Recherche normal avec prix et/ou catégorie
    public function findByPrix($prix) {
        return $this->createQueryBuilder('bdd')
            ->andWhere('bdd.prix < :val')
            ->setParameter('val', $prix)
            ->orderBy('bdd.prix', 'ASC')
            ->getQuery()->getResult();
    }

    public function findByPrixCategorie($prix, $categorie) {
        return $this->createQueryBuilder('bdd')
            ->andWhere('bdd.prix < :val',"bdd.categorie = $categorie")
            ->setParameter('val', $prix)
            ->orderBy('bdd.prix', 'ASC')
            ->getQuery()->getResult();
    }

    // Recherche dans l'alimentaire avec prix et unité
    public function findByPrixTypeCategorie($prix, $type, $categorie) {
        return $this->createQueryBuilder('bdd')
            ->andWhere('bdd.prix < :val', 'bdd.type = :valT', 'bdd.categorie = :valC')
            ->setParameter('val', $prix)
            ->setParameter('valT', $type)
            ->setParameter('valC', $categorie)
            ->orderBy('bdd.prix', 'ASC')
            ->getQuery()->getResult();
    }

    // /**
    //  * @return Articles[] Returns an array of Articles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Articles
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
