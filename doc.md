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
### GH
* ich tendiere auch eher zu einem Objekt als einem array. VuFind benutzt Zend\Config.  https://github.com/zendframework/zend-config/blob/master/src/Config.php#L24 Auch wenn das Config Objekt unseres Adapters diese drei Schnittstellen implementiert, kann man die Typen nicht einfach austauschen (oder bei PHP wäre das möglich, was ich aber nicht glaube) Möglichkeit: das Backend von VuFind wandelt das Zend Config Objekt über die Transformation in ein Array in ein Adapter Config Objekt

* Etwas Ähnliches mache ich bereits jetzt mit dem Query Objekt. https://github.com/linked-swissbib/adapterElasticsearch/blob/master/src/ElasticsearchAdapter/UserQuery/UserQuery.php Dieses implementiert jetzt mehr oder weniger das Query Objekt von VuFind. Dafür konvertiere ich das Query Objekt vn VuFind in das UserQuery Objekt https://github.com/linked-swissbib/vufind/blob/feature/getTogether/module/LinkedSwissbib/src/LinkedSwissbib/Backend/Elasticsearch/Backend.php#L110 Egal wie Du das Query Objekt implementierst (nimm Deine Ideen) ich kann das dann auf der VuFind Seite in Deine Struktur überführen.
Was genau enthält das Params Objekt? In VuFind sind dies eher allgemeine Angaben (die für die Suche benötigt werden)
z.B. Offset oder Highlighting ja oder nein (lezteres benötigen wir für die REST Schnittstelle nicht)


## Params

```php
public function setParam(string $name, string $value);
public function getParam(string $name) : string;
```
### GH
* so ist das auch in VuFind

## Config
Do we need a config object instead of a config array?
* mein Kommentar oben

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
          _all: {q}

  document_id:
    index: testsb
    type: document
    query: 
      ids:
        values: {id}
        
        
  example:
    index: testsb
    type: document
    query: 
      match:
        _all: {q}
    filter: 
      term: 
        name: {name}
    aggregation:
      something: {some}
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


### GH
* ich denke vieles (das Meiste) wird durch Deinen Vorschlag abgedeckt
* Mir ist gestern längere Zeit noch die Frage durch den Kopf, wie aufwendig (komplex) die Schnittstelle gestaltet werden soll: (dies auch bezogen auf Deine Frage, ob wir so etwas wie eine advanced search abdecken müssen)
* für mich gibt es zwei Fälle der Suche: 
  * eher einfache Suche. Dabei wird die URL als query benutzt
  * data.swissbib.ch/bibliographicResource/q=my query
  * oder data.swissbib.ch/person/name=Johann Sebastian Bach
  * solche Varianten bietet auch Lobid an (http://lobid.org/api)
  * ich denke dass wir das anbieten müsse und ich gehe davon aus, dass solche Art der Suchen am häufigsten verwendet werden
  * was machen wir mit advanced searches (Verknüpfen von mehreren Suchgruppen zu einer Gesamtsuche - bei ihnen heisst das QueryGroups?
  * Mein Vorschlag: Was hälst Du davon, wenn wir hier die Möglichkeit anbieten, die ES Query-DSL von einem client formulieren zu lassen? (ich nenne das mal den extended mode)
  * damit wären dann Abfragen wie die folgende möglich (als einfachste Form). 
```json  
  GET _search
{
  "query": {
    "match_all": {}
  }
}
```
  * diese können vom client definert und an unsere Schnittstelle geschickt werden. Nachteil für den client: Er muss die DSL con Elasticserach sowie das Mapping unseres Index kennen. Dafür hat er jedoch alle Abfragemöglichkeiten. 
  * was hälst Du davon? @Melanie: Das ist für mich auch ei Thema, was man konzeptionell im REST Kontext bearbeiten könnte. Ich bin vor einiger Zeit mal über Gednaken bei Elasticsearch gestolpert, wo sie diskutieren wie sehr REST-full ihre Schnitsttelle ist. So etwas könnte man, denke ich, mit aufnehmen.
  * in unseren templates müssen Filter abgebildet werden können. Das hast Du ja bereits vorgesehen. Hintergrund: wir werden in unserem Index Daten haben, die nicht den Bedingungen von CC0 entsprechen und deshalb über die Schnittstelle ausgeliefert werden dürfen. Wenn wir de n extended mode umsetzen, muss der Filter einer vom user definierten query hinzugefügt werden können. Ich denke das sollte aber möglich sein.
  * ich habe mir auch nochmals die searchspec von VuFind angesehen
  https://github.com/swissbib/vufind/blob/master/local/config/vufind/searchspecs.yaml
  https://github.com/swissbib/vufind/blob/master/config/vufind/searchspecs.yaml (Original von VuFind mit Erläuterungen)
  * Einen Teil der Funktionalität machen sogenannte MungeTpes aus. Dort werden Suchterme durch den code nochmals angepasst oder Bedingen wie Operatoren für bestimmte Suchen gesetzt. Ich denke, dass brauchen wir im Moment nicht so ausgeprägt, denke auch wir können es für den Moment weglassen.
  * Was ich in Deinem template so noch nicht gesehen habe: Du verwendest einzelne Feld-(Index) namen und ordnest ihnen eine Variable zu, so diese mit dem Value aus dem Code gesetzt werden kann. modified: {modified}
  * brauchen wir das in dieser Form so ausgeprägt?
  Solr kennt den sogenannten edismax parser, ES benutzt dafür unter anderem einen Typ cross_fields Beispiel:
  https://github.com/linked-swissbib/vufind/blob/feature/getTogether/local/config/vufind/searchspecsES.yaml#L90
  (es gibt noch weitere Arten von diesen Typen). Damit ist es möglich, einen oder mehrere Suchterme auf meherer Indexfelder gleichzeitig zu mappen. Das hatte ich für ES mal so begonnen (s. Beipiel) und wurde auch von Chur benutzt. Fände es gut, wenn wir eine solche Möglichkeit auch bei uns integrieren. (es würde wohl auch vereinfachen)
  
