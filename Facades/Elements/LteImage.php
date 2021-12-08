<?php
namespace exface\AdminLTEFacade\Facades\Elements;

use exface\Core\Widgets\Image;
use exface\Core\Facades\AbstractAjaxFacade\Elements\HtmlImageTrait;

/**
 *
 * @method Image getWidget()
 *        
 * @author Andrej Kabachnik
 *        
 */
class LteImage extends lteDisplay
{
    use HtmlImageTrait;
    
    /**
     *
     * @see AbstractJqueryElement::buildHtml()
     */
    public function buildHtml()
    {
        return $this->buildHtmlImage($this->getWidget()->getUri());
    }
    
    /**
     *
     * @see AbstractJqueryElement::buildJs()
     */
    public function buildJs()
    {
        return '';
    }
    
    public function buildCssElementClass()
    {
        return 'img-responsive';
    }
}
?>