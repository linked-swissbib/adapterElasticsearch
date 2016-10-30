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
  
### MM

  * Den Typ cross_fields habe ich nicht gekannt, ich denke das sollten wir aber sicherlich unterstützen. 
  So wie ich das sehe ist das aber eher eine Alternative zum Feld _all. Die Konfiguration, wie ich sie eingetragen habe
  würde eine Suche à la lobid erlauben, indem mit dem Parameter {name} nach name gesucht wird und im Feld modified nach {modified} und 
  nicht in beiden Feldern nach beidem.
  * Was mir aus dem Code nicht ganz klar wird, ist wie du die Parameter ins Query einfügst, kannst du mir da noch auf die Sprünge helfen?
  Wenn du z.B. ein match Query machst, wo werden da die Suchparameter (z.B. name="Markus") in das Query eingefügt?
  Die Implementierung, wie ich das im Moment im Branch develop mache, kann sicher nicht so bleiben, ich denke ich muss die Parameter bereits beim
  Builden des Queries fix einfügen. 
  * Bezüglich "Advanced Search" mittels ES Query-DSL: Ich denke das wäre durchaus eine Möglichkeit, die dem Client maximale Flexibilität bietet. 
  Ich weiss allerdings nicht, ob wir noch gross Zeit haben dies zu entwickeln, evtl. können wir es auch einfach konzeptionell mal aufnehmen. Ich denke, 
  wenn man diese Anfragen mehr oder weniger eins zu eins an den Elasticsearch Server weitergibt, dass man dann sehr gut aufpassen muss, dass der Client
  nur Dinge machen kann, die er auch darf. Für unser Projekt sehe ich erstmal die Priorität so, dass wir eine Schnittstelle à la lobid.org anbienten können.

### GH
  * zu Punkt 1 und 2:
  Feld _all und cross_fields würde ich nicht gleich setzen obwohl sie auf den ersten Blick so wirken. _all ist, so würde ich das einordnen eine Hilfskonstruktion von Elasticsearch um über die gesamte Struktur des Satzen suchen zu können. Man sollte es eigentlich nur im schnellen development verwenden. Ein Problem von _all: Du kannst keine Analyser verwenden. Damit ist die Suche auf einem sehr tiefen Niveau. Wenn man jedoch auf einezelnen Feldern sucht, so können diese Felder einer bestimmten Textanalyse unterzogen werden. Das gibt einem sehr viel mehr Möglichkeiten die Terme aufzubereiten. Der Trick beim cross_fields (es gibt noch mehr davon) ist nun, dass Du nicht einzelne Feldnamen angeben musst und diese in der Art von key-value pairs verknüpfst sondern Du gibst mit dem key fields an über welche Felder Du suchen möchtest und im key query die von Benutzer angegeben Suche (Beispiel: https://www.elastic.co/guide/en/elasticsearch/guide/current/_cross_fields_queries.html ) Damit würden wir uns die Feldnamen, wie sie von lobid verwendet werden, sparen und könnten in einer Konfiguration flexibel angeben, über welche Felder bei einem Typ gesucht werden soll. 
  Was wir brauchen ist die Angabe im Query-Teil der URL, welcher Teil der Konfiguration für die Suche verwendet werden soll (Bei VuFind wird diese Angabe SearchHandler genannt. Beispiel: https://www.swissbib.ch/Search/Results?lookfor=Polit,%20Denise%20F.&type=Author hier wird aus der Konfiguration der Teil Author genommen. https://github.com/swissbib/vufind/blob/master/local/config/vufind/searchspecs.yaml#L99 
  Die Idee meines Entwurfs war dann, durch die Angabe eines solchen Werts den entsprechenden Teil der Konfiguration wählen kann. Diese gibt dann komplett an, wie die Suche aufgebaut werden kann. Weiss nicht, ob ich das hier vernünftig erklären kann.
  Als ein Vorschlag (mit dem Ziel der Vereinfachung und das es nicht zu kompliziert wird)
    * die Suchen werden so aufgebaut
    data.swissbib.ch/bibliographicResource/q=hello world[weietere wie bei lobid u.a. zur Angabe des Formats]
    bibliographicResource als Teil der URL bestimmt den "SearchHandler" der Konfiguration. Mit der Definition aus der Konfiguration kann dann die DSL aufgebaut werden. q ist der Wert für die Suche
    Damit würden wir uns die Agaben von einzelnen Feldnamen, wie es lobid macht, sparen und könnten uns darauf konzentrieren, die Suche nach Massgabe der Konfiguration aufzubauen. 
    Wenn man spezifizierte Suchen für bibliographicResource haben möchte, könnte man z.B. angeben
    data.swissbib.ch/bibliographicResource/q=hello world[weietere wie bei lobid u.a. zur Angabe des Formats]&handler=xxx

  * Durchreichen der DSL
  Ja man muss schon aufpassen was man macht. Aber: über die REST Schnittstelle steuern wir ja schon, dass nur auf den _search Endpunkt zugegriffen wird und nicht auf andere, mit denen man Unheil anrichten könnte. Im Moment sehe ich keinen Fall, wo dies zu Schwierigkeiten führen würde
  
  * zur Zeit:
  ich habe aus den Augen verloren, wann Ihr mit der Arbeit fertig sein müsst. Ich denke wir sollten wieder einmal gemeinsam ein Zeitmanagement aufstellen.
  * nicht gut für die Arbeit ist, dass ich nächste Woche für eine Woche in den Ferien bin. Aber ich brauche auch mal ein paar freie Tage...
  
