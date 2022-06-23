<?php
namespace exface\AdminLTEFacade\Facades;

use exface\Core\Facades\AbstractAjaxFacade\AbstractAjaxFacade;
use exface\Core\Facades\AbstractAjaxFacade\Middleware\JqueryDataTablesUrlParamsReader;
use exface\Core\Interfaces\DataSheets\DataSheetInterface;
use exface\Core\Interfaces\WidgetInterface;
use exface\Core\Interfaces\Selectors\FacadeSelectorInterface;

class AdminLTEFacade extends AbstractAjaxFacade
{
    /**
     * 
     * @param FacadeSelectorInterface $selector
     */
    public function __construct(FacadeSelectorInterface $selector)
    {
        parent::__construct($selector);
        $this->setClassPrefix('lte');
        $this->setClassNamespace(__NAMESPACE__);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\AbstractAjaxFacade::getMiddleware()
     */
    protected function getMiddleware() : array
    {
        $middleware = parent::getMiddleware();
        $middleware[] = new JqueryDataTablesUrlParamsReader($this, 'getInputData', 'setInputData');
        return $middleware;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\Facades\HttpFacadeInterface::getUrlRoutePatterns()
     */
    public function getUrlRoutePatterns() : array
    {
        return [
            "/[\?&]tpl=adminlte/",
            "/\/api\/adminlte[\/?]/"
        ];
    }
    
    public function buildResponseData(DataSheetInterface $data_sheet, WidgetInterface $widget = null)
    {
        $data = array();
        $data['data'] = $data_sheet->getRowsDecrypted();
        $data['recordsFiltered'] = $data_sheet->countRowsInDataSource();
        $data['recordsTotal'] = $data_sheet->countRowsInDataSource();
        $data['footer'] = $data_sheet->getTotalsRows();
        return $data;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\AbstractAjaxFacade::getPageTemplateFilePathDefault()
     */
    protected function getPageTemplateFilePathDefault() : string
    {
        return $this->getApp()->getDirectoryAbsolutePath() . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'LtePageTemplate.html';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\AbstractAjaxFacade::getPageTemplateFilePathForUnauthorized()
     */
    protected function getPageTemplateFilePathForUnauthorized() : string
    {
        return $this->getApp()->getDirectoryAbsolutePath() . DIRECTORY_SEPARATOR . 'Facades' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'LteMessagePageTemplate.html';
    }
    
    /**
     *
     * {@inheritdoc}
     * @see AbstractAjaxFacade::getSemanticColors()
     */
    public function getSemanticColors() : array
    {
        $colors = parent::getSemanticColors();
        if (empty($colors)) {
            $colors = [
                '~OK' => 'lightgreen',
                '~WARNING' => 'yellow',
                '~ERROR' => 'orangered'
            ];
        }
        return $colors;
    }
}