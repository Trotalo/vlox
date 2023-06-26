<?php
abstract class VloxBaseManagerController extends modExtraManagerController {
    /** @var \Vlox\Vlox $vlox */
    public $vlox;

    public function initialize(): void
    {
        $this->vlox = $this->modx->services->get('vlox');

        $this->addCss($this->vlox->getOption('cssUrl') . 'mgr.css');
        $this->addJavascript($this->vlox->getOption('jsUrl') . 'mgr/vlox.js');

        $this->addHtml('
            <script type="text/javascript">
                Ext.onReady(function() {
                    vlox.config = '.$this->modx->toJSON($this->vlox->config).';
                });
            </script>
        ');

        parent::initialize();
    }

    public function getLanguageTopics(): array
    {
        return array('vlox:default');
    }

    public function checkPermissions(): bool
    {
        return true;
    }
}
