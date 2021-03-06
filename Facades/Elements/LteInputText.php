<?php
namespace exface\AdminLTEFacade\Facades\Elements;

class LteInputText extends lteInput
{

    function buildHtml()
    {
        $requiredScript = $this->getWidget()->isRequired() ? 'required="true"' : '';
        $disabledScript = $this->getWidget()->isDisabled() ? 'disabled="disabled"' : '';
        
        $output = <<<HTML

                        {$this->buildHtmlLabel()}
                        <textarea class="form-control"
                                name="{$this->getWidget()->getAttributeAlias()}"
                                id="{$this->getId()}"
                                style="height: {$this->getHeight()}; width: 100%;" 
                                {$requiredScript}
                                {$disabledScript}>{$this->getWidget()->getValueWithDefaults()}</textarea>
HTML;
        
        return $this->buildHtmlGridItemWrapper($output);
    }

    function buildJs()
    {
        $output = parent::buildJs();
        
        // Das Layout des Containers wird erneuert wenn das InputText die Groesse veraendert.
        if ($layoutWidget = $this->getWidget()->getParentByClass('exface\\Core\\Interfaces\\Widgets\\iLayoutWidgets')) {
            $output .= <<<JS

    $("#{$this->getId()}").on("resize", function() {
        {$this->getFacade()->getElement($layoutWidget)->buildJsLayouter()};
    });
JS;
        }
        
        return $output;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildCssHeightDefaultValue()
     */
    protected function buildCssHeightDefaultValue()
    {
        return ($this->getHeightRelativeUnit() * 2) . 'px';
    }
}
?>