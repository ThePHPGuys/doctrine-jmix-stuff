<?php
require_once __DIR__ . '/boostrap.php';

$query = $entityManager->createQuery('z = 1 OR 5!=:preset AND bla.z=567');
$parser = new \Doctrine\ORM\Query\Parser($query);
dump($parser->getLexer()->moveNext());
dump($parser);

dump($parser->ConditionalExpression());
dump($parser);