<?php

namespace ElasticsearchAdapter\UserQuery;


interface UserQueryInterface
{

    //the next three methods are coming from VuFind - are they needed?

    /**
     * Does the query contain the specified term?
     *
     * @param string $needle Term to check
     *
     * @return bool
     */
    public function containsTerm($needle);

    /**
     * Get a concatenated list of all query strings within the object.
     *
     * @return string
     */
    public function getAllTerms();

    /**
     * Replace a term.
     *
     * @param string $from Search term to find
     * @param string $to   Search term to insert
     *
     * @return void
     */
    public function replaceTerm($from, $to);


    public function getString();

    public function setString($string);

    public function getHandler();

    public function setHandler($handler);

    public function getOperator();

    public function setOperator($operator);




}