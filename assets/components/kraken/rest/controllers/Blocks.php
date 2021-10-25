<?php

require_once MODX_BASE_PATH . "modxMonster/vendor/autoload.php";

use ScssPhp\ScssPhp\Compiler;

class KrakenBlocks extends  modRestController {
  /** @var string $classKey The xPDO class to use */
  public $classKey = 'krakenBlock';
  /** @var string $defaultSortField The default field to sort by in the getList method */
  public $defaultSortField = 'id';
  /** @var string $defaultSortDirection The default direction to sort in the getList method */
  public $defaultSortDirection = 'ASC';

  /** @var ScssPhp\ScssPhp\Compiler $scss*/
  private $scss;

  private $defaultContent = <<<STR
      <template>
      <p>Create some content!</p>
      </template>
      <script>
      module.exports = {
        name: "[[+componentName]]",
        data() {
          return {
            krakenBlock: [[+blockContent]]
          };
        },
      };
      </script>
      <style scope>
      //Place styles here
      </style>
    STR;

  public function __construct(modX $modx,modRestServiceRequest $request,array $config = array()){
    parent::__construct($modx, $request, $config);
    $this->scss = new Compiler();
  }


  /**
   * This method can receive a number, to search for the component, or by name, to validate
   * if a give block exists
   * @param string $id
   */
  public function read($id) {

    if (is_numeric( $id )) {
      /** @var krakenBlock $respurce */
      parent::read($id);
      //with the stored block we read the file and retrieve ewach section to be editable on the front
      $chunkName = $this->object->get('chunkName');
      $chunk = $this->modx->getObject('modChunk', array('name'=>$chunkName));
      $blockContent = $chunk->get('snippet');

      /*$document = new \Gt\Dom\HTMLDocument($blockContent);
      $htmlSection = $document->querySelector("template")->innerHTML;
      $scriptSection = $document->querySelector("script")->textContent;
      $styleSection = $document->querySelector("style")->textContent;*/
      $templateEnd = strrpos($blockContent, '</template>');
      $scriptEnd = strpos($blockContent, '</script>', $templateEnd);
      $styleEnd = strpos($blockContent, '</style>');
      $scriptStart = strpos($blockContent, '<script>');
      $styleStart = strpos($blockContent, '<style');
      $htmlSection = substr($blockContent, strpos($blockContent, '<template>') + 10, $templateEnd - 10);
      $scriptSection = substr($blockContent,  $scriptStart + 8, ( $scriptEnd - $scriptStart ) - 8) ;
      $styleSection = substr($blockContent, $styleStart + 13, ($styleEnd - $styleStart) - 13) ;

      $returnObject = array(
        'htmlSection' => $htmlSection,
        'scriptSection' => $scriptSection,
        'styleSection' => $styleSection
      );
      $returnObject =  array_merge( $returnObject, (array) $this->object->_fields);
      //Before creating a new registry, we make sure the respurce its clean
      $this->modx->removeCollection('krakenBlocksResourceContent', array('resourceId'=> 3));
      /** @var krakenBlocksResourceContent $blockPreviewObj */
      $blockPreviewObj = $this->modx->newObject('krakenBlocksResourceContent',
        array('position'=> 1,
          'title'=> 'algo',
          'description'=>'add algo',
          'blockId'=> $this->object->_fields['id'],
          'resourceId'=> 3,
          'properties'=> $this->object->_fields['properties']));
      $blockPreviewObj->save();
      $this->modx->cacheManager->refresh();
      return $this->success('Succesful call!', $returnObject);
    } else {
      $block = $this->modx->getObject('krakenBlock', array('chunkName'=>$id));
      return $this->success('Succesful call!', $block);
    }

  }

  /**
   * @return array|void
   */
  public function put() {
    $resContent = $this->getProperties();
    $chunkName = $resContent['chunkName'];
    $description = $resContent['description'];

    $chunk = $this->modx->getObject('modChunk', array('name'=>$chunkName));
    if (isset($chunk)) {
      //With te stored chunck now we

      /*$document = new \Gt\Dom\HTMLDocument($this->defaultContent);
      $document->formatOutput = true;
      $document->querySelector("template")->innerHTML = $resContent['htmlSection'];
      $document->querySelector("script")->textContent = $resContent['scriptSection'];
      $document->querySelector("style")->textContent = $resContent['styleSection'];
      $finalBlock = $document->body->innerHTML;*/
      $finalBlock = "<template>\n";
      $finalBlock .= $resContent['htmlSection'];
      $finalBlock .= "</template>\n<script>";
      $finalBlock .= $resContent['scriptSection'];
      $finalBlock .= "</script><style scope>";
      $finalBlock .= $resContent['styleSection'];
      $finalBlock .= "</style>";
      $chunk->set('snippet', $finalBlock);
      $chunk->save();

    } else {
      /** @var modChunk $chunk */
      $chunk = $this->modx->newObject('modChunk');
      $chunk->set('name',$chunkName);
      //$chunk->setContent('<p>Create some content!</p>');
      $chunk->setContent($this->defaultContent);
      $storedChunk =  $chunk->save();
      //and now we store the data into the kraken table
      /** @var krakenBlock $krakenBlock */
      $krakenBlock = $this->modx->newObject('krakenBlock');
      $krakenBlock->set('chunkName',$chunkName);
      $krakenBlock->set('title',$chunkName);
      $krakenBlock->set('description',$description);
      $objectToStore = (object)[name=> $chunkName,
                                items=> []];
      $krakenBlock->set('properties',json_encode($objectToStore));
      $krakenBlock->save();

      return $this->read($krakenBlock->get('id'));
    }
    return $this->success('updated', $resContent);
  }

  public function delete() {
    //First we retrieve the block to get the chunkName
    $id = $this->getProperty($this->primaryKeyField,false);
    if (empty($id)) {
      return $this->failure($this->modx->lexicon('rest.err_field_ns',array(
        'field' => $this->primaryKeyField,
      )));
    }
    $block = $this->modx->getObject('krakenBlock', $id);
    //Now we check if the block is in use, its is the case an error should be thrown
    $resBlocks = $this->modx->getCollection('krakenBlocksResourceContent', array('blockId' => $id) );
    if (isset($resBlocks) && !empty($resBlocks)) {
      if (sizeof($resBlocks) === 1 && array_values($resBlocks)[0]->title === 'algo') {
        array_values($resBlocks)[0]->remove();
        //check if the only place used is the single renderer
      } else {
        return $this->failure("Can't delete block, its used on one or more resources");
      }
    }
    //Finally if its possible we delete both the block and the chunk

    $chunk = $this->modx->getObject('modChunk', array('name' => $block->chunkName));
    if ($chunk) $chunk->remove();

    parent::delete();
  }
}