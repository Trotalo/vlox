<?php
$projectId = $scriptProperties['project'];
$currentProject = $modx->getOption('vlox.project');

if ($scriptProperties['build'] === '1' && !empty($currentProject)) {
    return "assets/components/$currentProject/$projectId";
} else {
    return "assets/$projectId";
}
//assets/[[+project]]
return;