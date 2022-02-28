<?php

include_once __DIR__.'/../../../core.php';

switch ($resource) {
    case 'attributi':

        $query = 'SELECT `id`, `valore` FROM mg_gruppo_attributi |where| ORDER BY valore ASC';

        foreach ($elements as $element) {
            $filter[] = 'id='.prepare($element);
        }
        if (!empty($search)) {
            $search_fields[] = 'valore LIKE '.prepare('%'.$search.'%');
            
        }

        break;
}