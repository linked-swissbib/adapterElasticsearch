<?php



namespace ElasticsearchAdapter\UserQuery;


interface UserQueryGroupInterface extends UserQueryInterface
{


    //perhaps not necessary
    public function addQueries(array $queries);

    public function addQuery(UserQueryInterface $query);

    public function getQueries();

}
