<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticsearchAdapter;


use ElasticsearchAdapter\UserQuery\ESParamBag;
use ElasticsearchAdapter\UserQuery\ESParamInterface;



class Adapter
{


    /**
     * @var ESQueryBuilder
     */
    protected $queryBuilder;


    /**
     * @var \ElasticsearchAdapter\Connector
     */
    protected $connector;




    /**
     * Constructor.
     *
     * @param Connector $connector SOLR connector
     *
     * @return void
     */
    public function __construct(Connector $connector, ESQueryBuilder $queryBuilder)
    {
        $this->connector    = $connector;
        $this->queryBuilder = $queryBuilder;
        $this->identifier   = null;
    }





    /**
     *
     */
    //todo: type of Query and params
    public function search($query, $offset = 0, $limit = 10,
                           $params = null
    )
    {
        //todo: do we expect a speial query type
        if (isset($params) && !$params instanceof ESParamInterface )
        {
            throw new \Exception ("invalid ParamBag type for ElasticSearch target");
        }

        //Todo;
        //at the moment I'm not sure how to use the ParamBag and QueryBuilder Type
        //are we going to do it in the same way as it is done in SOLR (where Param bag creates the list of key-value parameters for the
        //HTTP-Get query or is the QueryBuilder type responsible for the creation of the ES DSL specific structure
        //which is at the end a PHP array
        $params = $params ?: new ESParamBag();

        //$params->set('rows', $limit);
        //$params->set('start', $offset);

        $this->getQueryBuilder()->setParams($params);
        $esDSLParams = $this->getQueryBuilder()->build($query);



        $response   = $this->connector->search($esDSLParams);

        //todo: fetch the Metadadata for this search


        $collection = $this->createRecordCollection($response);
        //$this->injectSourceIdentifier($collection);

        return $collection;
        //return  [];
    }

    /**
     * Retrieve a single document.
     *
     * @param string $id Document identifier
     * @param ParamBag $params Search backend parameters
     *
     * @return \VuFindSearch\Response\RecordCollectionInterface
     */
    public function retrieve($id, ParamBag $params = null)
    {
        // TODO: Implement retrieve() method.
    }

    /**
     * Return query builder.
     *
     * Lazy loads an empty default QueryBuilder if none was set.
     *
     * //@return ESQueryBuilder
     */
    public function getQueryBuilder()
    {
        if (!$this->queryBuilder) {
            $this->queryBuilder = new ESQueryBuilder();
        }
        return $this->queryBuilder;
    }

    /**
     * Set the query builder.
     *
     * @param ESQueryBuilder $queryBuilder
     *
     * @return void
     */
    public function setQueryBuilder(ESQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }



    /// Internal API

    /**
     * Create record collection.
     *
     * @param string $json Serialized JSON response
     *
     * @return RecordCollectionInterface
     */
    protected function createRecordCollection($response)
    {
        return $this->getRecordCollectionFactory()
            ->factory($response);
    }

    /**
     * Deserialize JSON response.
     *
     * @param string $json Serialized JSON response
     *
     * @return array
     *
     * @throws BackendException Deserialization error
     */
    protected function deserialize($json)
    {
        $response = json_decode($json, true);
        $error    = json_last_error();
        if ($error != \JSON_ERROR_NONE) {
            throw new BackendException(
                sprintf('JSON decoding error: %s -- %s', $error, $json)
            );
        }
        $qtime = isset($response['responseHeader']['QTime'])
            ? $response['responseHeader']['QTime'] : 'n/a';
        $this->log('debug', 'Deserialized SOLR response', ['qtime' => $qtime]);
        return $response;
    }


}