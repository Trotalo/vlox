<?php


require_once MODX_BASE_PATH . 'config.core.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';

class SchemaGenerator
{
  private $modx;
  private $manager;
  private $generator;
  private $pathComponent;
  private $schema;
  public $package;
  public $classList = array();
  public $schemaList = array();

  function __construct()
  {
    $this->modx = new modX();
    $this->modx->initialize('mgr');
    $this->manager = $this->modx->getManager();
    $this->generator = $this->manager->getGenerator();
    $this->pathComponent = MODX_CORE_PATH . 'components/';
  }

  public function addNewTable($path)
  {
    echo '<h3>Creating new tables. If an error appears in the process, please check the next step</h3>';
    echo '<ol> <li>Verify that the path or route exits.</li>';
    echo '<li> All files must be on the first level and end with extension .xml , example: newSchema.xml </li>';
    echo '<li>Verify the schema of each file follow this 
          <a href=\"https://docs.modx.com/current/en/extending-modx/xpdo/custom-models/defining-a-schema/database-and-tables\" 
          target=\"_blank\"> rules </a> </li></ol>';
    if (file_exists($path)) {
      $xmlFiles = $path;
      $listXmlFiles = scandir($xmlFiles);
      $filteredFiles = array_map(function ($k) {
        return str_replace('.xml', '', $k);
      }, array_filter($listXmlFiles, function ($k) {
        return SchemaGenerator::endsWith($k, '.xml');//strpos($k, '.xml') > 0;
      }));
      echo '<ul>';
      foreach ($filteredFiles as $folder) {
        print_r("<li>Building <strong>{$folder}</strong> folder inside to {$this->pathComponent} </li>");
        $PathFolder = $this->pathComponent . $folder;
        $this->setupFoldersAndFiles($path, $PathFolder, $folder);
        $this->loadTableMysql($PathFolder);
        echo '<hr/>';
      }
      echo '</ul>';
      $this->modx->log(xPDO::LOG_LEVEL_DEBUG, 'Verify that all tables was created at mysql.');
      return true;
    } else {
      print_r("The <strong>{$path}</strong> was not found");
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, "The path <strong>{$path}</strong> was not found.");
      return false;
    }
  }

  private function setupFoldersAndFiles($xmlFiles, $setPathFolder, $folder)
  {
    rmdir($setPathFolder);
    mkdir($setPathFolder . '/model/' . 'schema', 0777, true);
    copy($xmlFiles . $folder . '.xml', $setPathFolder . '/model/schema/' . $folder . '.xml');
    $sources = array(
      'root' => MODX_CORE_PATH,
      'core' => MODX_CORE_PATH . 'components/' . $folder . '/',
      'model' => MODX_CORE_PATH . 'components/' . $folder . '/model/',
      'schema' => MODX_CORE_PATH . 'components/' . $folder . '/model/schema/',
      'schema_file' => MODX_CORE_PATH . 'components/' . $folder . '/model/schema/' . $folder . '.xml',
      'assets' => MODX_CORE_PATH . 'assets/components/' . $folder . '/',
    );
    $this->generator->parseSchema($sources['schema_file'], $sources['model']);
    $this->schema = simplexml_load_file($sources['schema_file']);
    print_r("<li>Folder <strong>{$folder}</strong> was created successfully. Verify that the files were created correctly</li>");
    $this->modx->log(xPDO::LOG_LEVEL_DEBUG, "Folder {$folder} was created successfully.");
  }

  private function loadTableMysql($pathSchemaXml)
  {
    $this->package = (string) $this->schema['package'];
    if ($this->modx->addPackage($this->package, $pathSchemaXml . '/model/', 'modx_')) {
      print_r("<li>Adding package <strong>{$this->package}</strong></li>") ;
      echo '<ol>';
      foreach ($this->schema->object as $objectModel) {
        $className = (string)$objectModel['class'];
        $this->manager->removeObjectContainer($className);
        $this->manager->createObjectContainer($className);
        $this->modx->log(xPDO::LOG_LEVEL_DEBUG, "The table {$className} exist");
        array_push($this->classList,$className);
        array_push($this->schemaList,$objectModel);
        print_r("<li>The table <strong>{$className}</strong> was created successfully. </li>");
        var_dump($objectModel);
        echo '<br>';
        $this->modx->log(xPDO::LOG_LEVEL_DEBUG, "The table {$className} was created successfully.");
      }
      echo '</ol>';
    } else {
      print_r("Verify the schema of {$this->package} . For more information follow this
        <a href=\"https://docs.modx.com/current/en/extending-modx/xpdo/custom-models/defining-a-schema/database-and-tables\" 
        target='_blank'> link </a> <br>");
      echo '<br>';
      var_dump($this->schema->object);
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, "Verify the structure of {$this->package} .");
    }
  }

  /**
   * Taken from https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
   * @param $haystack
   * @param $needle
   * @return bool
   */
  private static function endsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
      return true;
    }
    return substr( $haystack, -$length ) === $needle;
  }
}