<x-layouts.table 
	:controller="$controller" 
	:datas="$projects" 
	:related="$terr" 
	:txtrelated="'territory'" 
	:urlrelated="'territories'" 
    :actions="[
        ['name' => 'Inform', 'color' => 'warning', 'action' => $controller->getParams('view') . '.inform', 'url' => '' ],
        ['name' => 'Play', 'color' => 'play', 'action' => '', 'url' => '/player/?id=[$data->id]' ],
    ]"
/>