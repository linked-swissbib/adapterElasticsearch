# Public interfaces

## Adapter

```php
public function __construct(array $config); //Probably also Config object instead of array
public function search(string $searchType, Params $params) : array; //returns raw elasticsearch response
```

or 

```php
public function __construct(array $config);
public function search(Query $query, Params $params) : array;
```

## Params

```php
public function setParam(string $name, string $value);
public function getParam(string $name) : string;
```

## Config
Do we need a config object instead of a config array?

# Config
Terms in curly brackets e.g. {q} will be provided in the Params object passed to the search method.

```yml
hosts: [localhost]
templates: 
  all_types:
    index: testsb
    type: [bibliographicResource,document,item,organization,person,work]
    query:
      match:
        _all: {q}

  document:
    index: testsb
    type: document
    query: 
      bool:
        should:
          local: {local}
          contributor: {contributor}
          issued: {issued}
          modified: {modified}
          primaryTopic: {primaryTopic}

  document_id:
    index: testsb
    type: document
    query: 
      ids:
        values: {id}
```

# Usage

```php
$config = loadConfig();
$params = buildParamsFromRequest($request);
$adapter = new Adapter($config);

$adapter->search('all_types', $params);
```

or 

```php
$config = loadConfig();
$params = buildParamsFromRequest($request);
$queryBuilder = new QueryBuilder($config);
$query = $queryBuilder->buildQueryFromTemplate('all_types');
$adapter = new Adapter($config);

$adapter->search($query, $params);
```