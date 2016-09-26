<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticsearchAdapter\DSLBuilder;


interface UserQueryAwareInterface
{
    //todo: do we need a special QueryType - was VuFind Abstract-Query
    public function setUserQuery( $userQuery);
    public function setSearchSpec(array $searchSpec);
    public function getUserQuery();
    public function getSearchSpec();
}