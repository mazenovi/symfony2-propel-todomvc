<?php

namespace Mazenovi\UserBundle\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use FOS\UserBundle\Propel\User;
use Mazenovi\UserBundle\Model\FosUserExtra;
use Mazenovi\UserBundle\Model\FosUserExtraPeer;
use Mazenovi\UserBundle\Model\FosUserExtraQuery;

/**
 * @method FosUserExtraQuery orderById($order = Criteria::ASC) Order by the id column
 * @method FosUserExtraQuery orderByFosUserId($order = Criteria::ASC) Order by the fos_user_id column
 * @method FosUserExtraQuery orderByHomePage($order = Criteria::ASC) Order by the home_page column
 *
 * @method FosUserExtraQuery groupById() Group by the id column
 * @method FosUserExtraQuery groupByFosUserId() Group by the fos_user_id column
 * @method FosUserExtraQuery groupByHomePage() Group by the home_page column
 *
 * @method FosUserExtraQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FosUserExtraQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FosUserExtraQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FosUserExtraQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method FosUserExtraQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method FosUserExtraQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method FosUserExtra findOne(PropelPDO $con = null) Return the first FosUserExtra matching the query
 * @method FosUserExtra findOneOrCreate(PropelPDO $con = null) Return the first FosUserExtra matching the query, or a new FosUserExtra object populated from the query conditions when no match is found
 *
 * @method FosUserExtra findOneById(int $id) Return the first FosUserExtra filtered by the id column
 * @method FosUserExtra findOneByFosUserId(int $fos_user_id) Return the first FosUserExtra filtered by the fos_user_id column
 * @method FosUserExtra findOneByHomePage(string $home_page) Return the first FosUserExtra filtered by the home_page column
 *
 * @method array findById(int $id) Return FosUserExtra objects filtered by the id column
 * @method array findByFosUserId(int $fos_user_id) Return FosUserExtra objects filtered by the fos_user_id column
 * @method array findByHomePage(string $home_page) Return FosUserExtra objects filtered by the home_page column
 */
abstract class BaseFosUserExtraQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFosUserExtraQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Mazenovi\\UserBundle\\Model\\FosUserExtra', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new FosUserExtraQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     FosUserExtraQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FosUserExtraQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FosUserExtraQuery) {
            return $criteria;
        }
        $query = new FosUserExtraQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$id, $fos_user_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   FosUserExtra|FosUserExtra[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FosUserExtraPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FosUserExtraPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return   FosUserExtra A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `FOS_USER_ID`, `HOME_PAGE` FROM `fos_user_extra` WHERE `ID` = :p0 AND `FOS_USER_ID` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new FosUserExtra();
            $obj->hydrate($row);
            FosUserExtraPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return FosUserExtra|FosUserExtra[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|FosUserExtra[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return FosUserExtraQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(FosUserExtraPeer::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(FosUserExtraPeer::FOS_USER_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FosUserExtraQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(FosUserExtraPeer::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(FosUserExtraPeer::FOS_USER_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FosUserExtraQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FosUserExtraPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the fos_user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFosUserId(1234); // WHERE fos_user_id = 1234
     * $query->filterByFosUserId(array(12, 34)); // WHERE fos_user_id IN (12, 34)
     * $query->filterByFosUserId(array('min' => 12)); // WHERE fos_user_id > 12
     * </code>
     *
     * @see       filterByUser()
     *
     * @param     mixed $fosUserId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FosUserExtraQuery The current query, for fluid interface
     */
    public function filterByFosUserId($fosUserId = null, $comparison = null)
    {
        if (is_array($fosUserId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FosUserExtraPeer::FOS_USER_ID, $fosUserId, $comparison);
    }

    /**
     * Filter the query on the home_page column
     *
     * Example usage:
     * <code>
     * $query->filterByHomePage('fooValue');   // WHERE home_page = 'fooValue'
     * $query->filterByHomePage('%fooValue%'); // WHERE home_page LIKE '%fooValue%'
     * </code>
     *
     * @param     string $homePage The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FosUserExtraQuery The current query, for fluid interface
     */
    public function filterByHomePage($homePage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($homePage)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $homePage)) {
                $homePage = str_replace('*', '%', $homePage);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FosUserExtraPeer::HOME_PAGE, $homePage, $comparison);
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FosUserExtraQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(FosUserExtraPeer::FOS_USER_ID, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FosUserExtraPeer::FOS_USER_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUser() only accepts arguments of type User or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the User relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FosUserExtraQuery The current query, for fluid interface
     */
    public function joinUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('User');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'User');
        }

        return $this;
    }

    /**
     * Use the User relation User object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \FOS\UserBundle\Propel\UserQuery A secondary query class using the current class as primary query
     */
    public function useUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'User', '\FOS\UserBundle\Propel\UserQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   FosUserExtra $fosUserExtra Object to remove from the list of results
     *
     * @return FosUserExtraQuery The current query, for fluid interface
     */
    public function prune($fosUserExtra = null)
    {
        if ($fosUserExtra) {
            $this->addCond('pruneCond0', $this->getAliasedColName(FosUserExtraPeer::ID), $fosUserExtra->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(FosUserExtraPeer::FOS_USER_ID), $fosUserExtra->getFosUserId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
