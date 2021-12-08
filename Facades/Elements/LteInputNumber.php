<?php
namespace exface\AdminLTEFacade\Facades\Elements;

class LteInputNumber extends lteInput
{
    /**
     * 
     * {@inheritDoc}
     * @see \exface\AdminLTEFacade\Facades\Elements\LteInput::getInputType()
     */
    protected function getInputType() : ?string
    {
        return 'number';
    }
}