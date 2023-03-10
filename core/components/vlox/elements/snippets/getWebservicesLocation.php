<?php

$currentProject = $modx->getOption('vlox.project');
return !empty($currentProject) ? "/$currentProject/" : '';
