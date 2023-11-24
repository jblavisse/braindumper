<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Memory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Memory>
 *
 * @method Memory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Memory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Memory[]    findAll(
 * @method Memory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemoryRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Memory::class);
    }


    // MemoryRepository.php

    // MemoryRepository.php

    // MemoryRepository.php

    // MemoryRepository.php

    // MemoryRepository.php

    public function findByUserGroupedByCategories($user)
    {
        $qb = $this->createQueryBuilder('m');

        $allCategories = $this->getEntityManager()->getRepository(Category::class)->findAll();

        $result = $qb
            ->select('c.name as categoryName, COALESCE(COUNT(m.id), 0) as memoryCount')
            ->leftJoin('m.category', 'c')
            ->andWhere('m.user = :user')
            ->setParameter('user', $user)
            ->groupBy('c.id, c.name') 
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $categoriesCount = [];

        foreach ($allCategories as $category) {
            $categoryName = $category->getName();
            $categoriesCount[$categoryName] = 0; 
        }

        foreach ($result as $groupedMemory) {
            $categoryName = $groupedMemory['categoryName'];
            $memoryCount = $groupedMemory['memoryCount'];

            $categoriesCount[$categoryName] = $memoryCount;
        }


        return $categoriesCount;
    }



    // public function findByUserGroupedByCategories($user)
    // {
    //     return $this->createQueryBuilder('m')
    //         ->select('c.name as categoryName, COUNT(m.id) as memoryCount')
    //         ->join('m.category', 'c')
    //         ->andWhere('m.user = :user')
    //         ->setParameter('user', $user)
    //         ->groupBy('c.id') // Assurez-vous de grouper par l'identifiant unique de la catégorie
    //         ->getQuery()
    //         ->getResult();
    // }

    //    /**
//     * @return Memory[] Returns an array of Memory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Memory
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}