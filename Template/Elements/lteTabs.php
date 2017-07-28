<?php
namespace exface\AdminLteTemplate\Template\Elements;

class lteTabs extends lteContainer
{

    function generateHtml()
    {        
        return <<<HTML
	<div id="{$this->getId()}" class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			{$this->buildHtmlTabHeaders()}
		</ul>
		<div class="tab-content">
			{$this->buildHtmlTabBodies()}
		</div>
	</div>
HTML;
    }
    
    protected function buildHtmlTabBodies()
    {
        $output = '';
        foreach ($this->getWidget()->getChildren() as $tab) {
            $output .= $this->getTemplate()->getElement($tab)->buildHtmlBody();
        }
        return $output;
    }
    
    protected function buildHtmlTabHeaders()
    {
        $output = '';
        foreach ($this->getWidget()->getChildren() as $tab) {
            $output .= $this->getTemplate()->getElement($tab)->buildHtmlHeader();
        }
        return $output;
    }
}
?>
