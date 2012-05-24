<?php

namespace Mazenovi\TodoMVCBundle\Model\om;

use \Criteria;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelPDO;
use Mazenovi\TodoMVCBundle\Model\Todo;
use Mazenovi\TodoMVCBundle\Model\TodoPeer;
use Mazenovi\TodoMVCBundle\Model\TodoQuery;

/**
 * Base class that represents a query for the 'todo' table.
 *
 * 
 *
 * @method     TodoQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     TodoQuery orderByContent($order = Criteria::ASC) Order by the content column
 * @method     TodoQuery orderByDone($order = Criteria::ASC) Order by the done column
 *
 * @method     TodoQuery groupById() Group by the id column
 * @method     TodoQuery groupByContent() Group by the content column
 * @method     TodoQuery groupByDone() Group by the done column
 *
 * @method     TodoQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     TodoQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     TodoQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     Todo findOne(PropelPDO $con = null) Return the first Todo matching the query
 * @method     Todo findOneOrCreate(PropelPDO $con = null) Return the first Todo matching the query, or a new Todo object populated from the query conditions when no match is found
 *
 * @method     Todo findOneById(int $id) Return the first Todo filtered by the id column
 * @method     Todo findOneByContent(string $content) Return the first Todo filtered by the content column
 * @method     Todo findOneByDone(boolean $done) Return the first Todo filtered by the done column
 *
 * @method     array findById(int $id) Return Todo objects filtered by the id column
 * @method     array findByContent(string $content) Return Todo objects filtered by the content column
 * @method     array findByDone(boolean $done) Return Todo objects filtered by the done column
 *
 * @package    propel.generator.src.Mazenovi.TodoMVCBundle.Model.om
 */
abstract class BaseTodoQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseTodoQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Mazenovi\\TodoMVCBundle\\Model\\Todo', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new TodoQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    TodoQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof TodoQuery) {
			return $criteria;
		}
		$query = new TodoQuery();
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
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    Todo|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = TodoPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(TodoPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    Todo A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `ID`, `CONTENT`, `DONE` FROM `todo` WHERE `ID` = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new Todo();
			$obj->hydrate($row);
			TodoPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    Todo|array|mixed the result, formatted by the current formatter
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
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
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
	 * @return    TodoQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(TodoPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    TodoQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(TodoPeer::ID, $keys, Criteria::IN);
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
	 * @return    TodoQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(TodoPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the content column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByContent('fooValue');   // WHERE content = 'fooValue'
	 * $query->filterByContent('%fooValue%'); // WHERE content LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $content The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    TodoQuery The current query, for fluid interface
	 */
	public function filterByContent($content = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($content)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $content)) {
				$content = str_replace('*', '%', $content);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(TodoPeer::CONTENT, $content, $comparison);
	}

	/**
	 * Filter the query on the done column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDone(true); // WHERE done = true
	 * $query->filterByDone('yes'); // WHERE done = true
	 * </code>
	 *
	 * @param     boolean|string $done The value to use as filter.
	 *              Non-boolean arguments are converted using the following rules:
	 *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    TodoQuery The current query, for fluid interface
	 */
	public function filterByDone($done = null, $comparison = null)
	{
		if (is_string($done)) {
			$done = in_array(strtolower($done), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(TodoPeer::DONE, $done, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Todo $todo Object to remove from the list of results
	 *
	 * @return    TodoQuery The current query, for fluid interface
	 */
	public function prune($todo = null)
	{
		if ($todo) {
			$this->addUsingAlias(TodoPeer::ID, $todo->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseTodoQuery