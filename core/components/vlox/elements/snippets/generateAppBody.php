<?php

if (!isset($isEditingVlox) || empty($isEditingVlox)) {
  throw new Error("can't call generateAppBody without a isEditingVlox parameter!");
}

if ($isEditingVlox === '1') {
  return  $modx->getChunk('editingAppChunk');
} else {
  return  $modx->getChunk('liveAppChunk');
}

