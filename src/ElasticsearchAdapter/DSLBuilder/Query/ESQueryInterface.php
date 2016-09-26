<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticsearchAdapter\DSLBuilder\Query;



interface ESQueryInterface
{
    public function build();
    public function getName();

    //todo: do we need a special QueryType - was VuFind Abstract-Query
    public function setUserQuery(ESQueryInterface $userQuery);
    public function setSearchSpec(array $searchSpec);

    /**
     * @return ESQueryInterface
     */
    public function getUserQuery();

    /**
     * @return array
     */
    public function getSearchSpec();


}