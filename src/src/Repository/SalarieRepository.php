<?php

namespace App\Repository;

use App\Entity\Salarie;
use App\Entity\SalarieUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Salarie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Salarie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Salarie[]    findAll()
 * @method Salarie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalarieRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salarie::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Salarie) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    //TODO: ATTENTION la recherche dans le json change en fonction du SGBD
    public function findByRole(string $role): array {
        $rsm = $this->createResultSetMappingBuilder('s');
        $rawQuery = sprintf("SELECT %s FROM salarie s WHERE JSON_SEARCH(s.roles, 'one', :role) IS NOT NULL", $rsm->generateSelectClause());
        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
        $query->setParameter('role', $role);
        return $query->getResult();
    }

    //TODO: ATTENTION la recherche dans le json change en fonction du SGBD
    public function findOneByRole(string $role) {
        $rsm = $this->createResultSetMappingBuilder('s');
        $rawQuery = sprintf(
            "SELECT %s 
                    FROM salarie s 
                    WHERE JSON_SEARCH(s.roles, 'one', :role) IS NOT NULL 
                    LIMIT 1",
                    $rsm->generateSelectClause()
        );
        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
        $query->setParameter('role', $role);
        return $query->getOneOrNullResult();
    }

    //TODO: ATTENTION la recherche dans le json change en fonction du SGBD
    public function findOneByRoleAndService(string $role, string $service) {
        $rsm = $this->createResultSetMappingBuilder('s');
        $rawQuery = sprintf(
            "SELECT %s 
                    FROM salarie s 
                    WHERE JSON_SEARCH(s.roles, 'one', :role) IS NOT NULL AND s.service = :service
                    LIMIT 1",
            $rsm->generateSelectClause()
        );
        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
        $query->setParameters(new ArrayCollection([
            new Parameter('role', $role),
            new Parameter('service', $service)
        ]));
        return $query->getOneOrNullResult();
    }

    public function findOneByEmail($value): ?Salarie
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
