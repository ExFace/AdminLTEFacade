<?php
namespace exface\AdminLTEFacade\Facades\Elements;

use exface\Core\DataTypes\BooleanDataType;

class LteInputCheckBox extends lteValue
{

    public function buildHtml()
    {
        $checkedScript = BooleanDataType::cast($this->getWidget()->getValueWithDefaults()) ? 'checked="checked"' : '';
        $disabledScript = $this->getWidget()->isDisabled() ? 'disabled="disabled"' : '';
        $output = <<<HTML

                    <div class="exf-input checkbox">
                        <label>
                            <input type="checkbox" value="1" name="{$this->getWidget()->getAttributeAlias()}" id="{$this->getWidget()->getId()}" {$checkedScript} {$disabledScript} /> 
                            {$this->getCaption()}
                        </label>
                    </div>
HTML;
        return $this->buildHtmlGridItemWrapper($output);
    }

    public function buildJs()
    {
        return '';
    }
    
    protected function getCaption() : string
    {
        return $this->getWidget()->isInTable() ? '' : parent::getCaption();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildJsValidator()
     */
    public function buildJsValidator(?string $valJs = null) : string
    {
        return 'true';
    }
}