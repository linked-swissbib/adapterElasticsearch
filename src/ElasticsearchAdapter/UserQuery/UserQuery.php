<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 16.09.16
 * Time: 12:25
 */

namespace ElasticsearchAdapter\UserQuery;


class UserQuery implements UserQueryInterface
{


    /**
     * Name of query handler, if any.
     *
     * @var string
     */
    protected $queryHandler;

    /**
     * Query string
     *
     * @var string
     */
    protected $queryString;

    /**
     * Operator to apply to query string (null if not applicable)
     *
     * @var string
     */
    protected $operator;



    public function containsTerm($needle)
    {
        // TODO: Implement containsTerm() method.
    }

    public function getAllTerms()
    {
        // TODO: Implement getAllTerms() method.
    }

    public function replaceTerm($from, $to)
    {
        // TODO: Implement replaceTerm() method.
    }

    public function getString()
    {
        return $this->queryString;
    }

    public function setString($string)
    {
        $this->queryString = $string;
    }

    public function getHandler()
    {
        return $this->queryHandler;
    }

    public function setHandler($handler)
    {
        $this->queryHandler = $handler;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function setOperator($operator)
    {
        $this->operator = $operator;
    }
}