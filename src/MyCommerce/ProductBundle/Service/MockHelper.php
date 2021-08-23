<?php
/**
 * Created by PhpStorm.
 * User: monzer
 * Date: 8/21/21
 * Time: 10:54 PM
 */
namespace MyCommerce\ProductBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MockHelper
{


    public static function getMock($phpunitTestcase,  $class = \stdClass::class, $returnValuesMap = array()){
        $mock = $phpunitTestcase->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->setMethods(array_keys($returnValuesMap))
            ->getMock();

        foreach ($returnValuesMap as $method => $returnValue) {
            $mock
                ->method($method)
                ->willReturn($returnValue);
        }

        return $mock;
    }

    /**
     * @param $phpunitTestcase
     * @param null $entityManager
     * @param null $connection
     * @param $entityClass
     * @return mixed
     */
    public static function getDoctrineMock($phpunitTestcase, $entityManager = NULL, $connection = NULL, $entityClass = \stdClass::class)
    {
        $doctrineMock = $phpunitTestcase->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getManager', 'getConnection', 'getRepository'))
            ->getMock();

        if (!$entityManager)
            $entityManager = MockHelper::getEntityManagerMock($phpunitTestcase, array(self::getRepoMock($phpunitTestcase, self::getEntityMock($phpunitTestcase, $entityClass))));

        $doctrineMock->method('getManager')
            ->willReturn($entityManager);

        if (!$connection)
            $connection = MockHelper::getConnectionMock($phpunitTestcase);

        $doctrineMock->method('getConnection')
            ->willReturn($connection);

        if($entityClass){
            $doctrineMock->method('getRepository')
                ->wilLReturn(self::getRepoMock($phpunitTestcase, self::getEntityMock($phpunitTestcase, $entityClass)));
        }
        else{
            $doctrineMock->method('getRepository')
                ->wilLReturn(self::getRepoMock($phpunitTestcase, null));
        }

        return $doctrineMock;
    }

    /**
     * @param $phpunitTestcase
     * @param array $repositories
     * @param array $queryResult
     * @return mixed
     */
    public static function getEntityManagerMock($phpunitTestcase, array $repositories, $queryResult = array())
    {
        $entityManagerMock = $phpunitTestcase->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getRepository', 'persist', 'flush',  'remove', 'createQuery', 'createQueryBuilder', 'getConnection'))
            ->getMock();

        if (count($repositories) < 2) {
            foreach ($repositories as $repository) { // TODO mock repository name to repository mock, see other methods
                $entityManagerMock
                    ->method('getRepository')
                    ->willReturn($repository);
            }
        } else {
            $entityManagerMock
                ->method('getRepository')
                ->with($phpunitTestcase->isType('string'))
                ->will($phpunitTestcase->returnCallback(function ($argument) use ($repositories) {
                    return $repositories[$argument];
                }));
        }


        $statementMock = MockHelper::getStatementMock($phpunitTestcase);

        $entityManagerMock
            ->method('getConnection')
            ->willReturn(self::getConnectionMock($phpunitTestcase, $statementMock));

        return $entityManagerMock;
    }


    /**
     * @param $phpunitTestcase
     * @param null $statement
     * @return mixed
     */
    public static function getConnectionMock($phpunitTestcase, $statement = NULL)
    {
        $connectionMock = $phpunitTestcase->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(array('prepare', 'fetch', 'query'))
            ->getMock();

        if (!$statement)
            $statement = MockHelper::getStatementMock($phpunitTestcase);

        $connectionMock->method('prepare')
            ->willReturn($statement);

        $connectionMock->method('query')
            ->willReturn($statement);

        return $connectionMock;
    }

    /**
     * @param $phpunitTestcase
     * @return mixed
     */
    public static function getStatementMock($phpunitTestcase)
    {
        $statementMock = $phpunitTestcase->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            //->setMethods(array('execute'))
            ->getMock();

        $statementMock->method('execute')
            ->willReturn(true);

        return $statementMock;
    }

    /**
     * This method returns a mock of a Doctrine repository for a given Entity.
     * The mock will respond to most common repository methods i.e. findOneBy findBy*
     * with an instance of the given |$entity|
     * @param $phpunitTestcase
     * @param $entity
     * @return mixed
     */
    public static function getRepoMock($phpunitTestcase, $entity)
    {
        $repoMock = $phpunitTestcase->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('findOneBy', 'findBy', 'find'))
            ->getMock();

        $repoMock
            ->method('find')
            ->willReturn($entity);
        $repoMock
            ->method('findOneBy')
            ->willReturn($entity);
        $repoMock
            ->method('findBy')
            ->willReturn(array($entity));


        return $repoMock;
    }

    /**
     * @param $phpunitTestcase
     * @param $entityClass
     * @return mixed
     */
    public static function getEntityMock($phpunitTestcase, $entityClass = \stdClass::class)
    {
        if($entityClass == null)
            return null;
        $entityMock = $phpunitTestcase
            ->getMockBuilder($entityClass)
            ->disableOriginalConstructor()
            ->getMock();

        return $entityMock;
    }
}