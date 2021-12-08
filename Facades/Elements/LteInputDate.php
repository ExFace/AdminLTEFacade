<?php
namespace exface\AdminLTEFacade\Facades\Elements;

use exface\Core\Facades\AbstractAjaxFacade\Elements\JqueryInputDateTrait;
use exface\Core\DataTypes\FilePathDataType;

// Es waere wuenschenswert die Formatierung des Datums abhaengig vom Locale zu machen.
// Das Problem dabei ist folgendes: Wird im DateFormatter das Datum von DateJs ent-
// sprechend dem Locale formatiert, so muss der DateParser kompatibel sein. Es kommt
// sonst z.B. beim amerikanischen Format zu Problemen. Der 5.11.2015 wird als 11/5/2015
// formatiert, dann aber entsprechend den alexa RMS Formaten als 11.5.2015 geparst. Der
// Parser von DateJs kommt hingegen leider nicht mit allen alexa RMS Formaten zurecht.

// Eine Loesung waere fuer die verschiedenen Locales verschiedene eigene Parser zu
// schreiben, dann koennte man aber auch gleich verschiedene eigene Formatter
// hinzufuegen.
// In der jetzt umgesetzten Loesung wird das Anzeigeformat in den Uebersetzungsdateien
// festgelegt. Dabei ist darauf zu achten, dass es kompatibel zum Parser ist, das
// amerikanische Format MM/dd/yyyy ist deshalb nicht moeglich, da es vom Parser als
// dd/MM/yyyy interpretiert wird.
class LteInputDate extends lteInput
{
    use JqueryInputDateTrait;

    private $bootstrapDatepickerLocale;

    /**
     * 
     * {@inheritDoc}
     * @see \exface\AdminLTEFacade\Facades\Elements\LteInput::buildHtml()
     */
    public function buildHtml()
    {
        $requiredScript = $this->getWidget()->isRequired() ? 'required="true" ' : '';
        $disabledScript = $this->getWidget()->isDisabled() ? 'disabled="disabled" ' : '';
        
        $output = <<<HTML

                {$this->buildHtmlLabel()}
                <div class="form-group input-group date">
                    <input class="form-control"
                        type="text"
                        name="{$this->getWidget()->getAttributeAlias()}"
                        id="{$this->getId()}"
                        value="{$this->getWidget()->getValueWithDefaults()}"
                        {$requiredScript}
                        {$disabledScript} />
                    <div class="input-group-addon" onclick="$('#{$this->getId()}').datepicker('show');">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>

HTML;
        return $this->buildHtmlGridItemWrapper($output);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\AdminLTEFacade\Facades\Elements\LteInput::buildJs()
     */
    function buildJs()
    {
        $languageScript = $this->getBootstrapDatepickerLocale() ? 'language: "' . $this->getBootstrapDatepickerLocale() . '",' : '';
        if ($this->getWidget()->isRequired()) {
            $validateScript = $this->buildJsFunctionPrefix() . 'validate();';
            $requiredScript = $this->buildJsRequired();
        }
        
        $output = <<<JS

    $("#{$this->getId()}").datepicker({
        // Bleibt geoeffnet wenn ein Datum selektiert wird. Gibt sonst Probleme, wenn
        // eine Datumseingabe per Enter abgeschlossen wird und anschliessend eine neue
        // Datumseingabe erfolgt.
        autoclose: false,
        format: {
            toDisplay: function (date) {
                // date ist ein date-Objekt und wird zu einem String geparst
                return (date instanceof Date ? {$this->getDateFormatter()->buildJsFormatDateObjectToString('date')} : '');
            },
            toValue: function(string, format, language) {
                return {$this->getDateFormatter()->buildJsFormatParserToJsDate('string')};
            }
        },
        {$languageScript}
        // Markiert das heutige Datum.
        todayHighlight: true
    });
    // Wird der uebergebene Wert per value="..." im HTML uebergeben, erscheint er
    // unformatiert (z.B. "-1d"). Wird der Wert hier gesetzt, wird er formatiert.
    $("#{$this->getId()}").datepicker("update", {$this->escapeString($this->getWidget()->getValueWithDefaults())});
    // Bei leeren Werten, wird die toValue-Funktion nicht aufgerufen, und damit der
    // interne Wert fuer die Rueckgabe des value-Getters nicht entfernt. Dies geschieht
    // hier.
    $("#{$this->getId()}").on("change", function() {
        var val = $("#{$this->getId()}").val();
        var input = $("#{$this->getId()}");
        if (val === '' || val === undefined || val === null) {
            input
                .data("_internalValue", "")
                .data("_isValid", false);
        } else {
            var date = {$this->getDateFormatter()->buildJsFormatParserToJsDate('val')};
            if (date) {
                input
                    .data("_internalValue", {$this->getDateFormatter()->buildJsFormatDateObjectToInternal('date')})
                    .data("_isValid", true);
            } else {
                input
                    .data("_internalValue", "")
                    .data("_isValid", false);
            }
            {$validateScript}
        }
    });
    
    {$requiredScript}

JS;
        
        return $output;
    }

    public function buildHtmlHeadTags()
    {
        $headers = parent::buildHtmlHeadTags();
        $datepickerBaseUrl = $this->getFacade()->buildUrlToSource('LIBS.BOOTSTRAP_DATEPICKER.FOLDER');
        $headers[] = '<script type="text/javascript" src="' . $datepickerBaseUrl . '/js/bootstrap-datepicker.js"></script>';
        if ($locale = $this->getBootstrapDatepickerLocale($datepickerBaseUrl)) {
            $headers[] = '<script type="text/javascript" src="' . $datepickerBaseUrl . '/locales/bootstrap-datepicker.' . $locale . '.min.js"></script>';
        }
        $headers[] = '<link rel="stylesheet" href="' . $datepickerBaseUrl . '/css/bootstrap-datepicker3.css">';
        
        $formatter = $this->getDateFormatter();
        $headers = array_merge($headers, $formatter->buildHtmlHeadIncludes($this->getFacade()), $formatter->buildHtmlBodyIncludes($this->getFacade()));
        return $headers;
    }

    /**
     * Generates the Bootstrap Datepicker Locale-name based on the Locale provided by
     * the translator.
     *
     * @return string
     */
    protected function getBootstrapDatepickerLocale()
    {
        if (is_null($this->bootstrapDatepickerLocale)) {
            $datepickerBasepath = $this->getWorkbench()->filemanager()->getPathToVendorFolder() . DIRECTORY_SEPARATOR . FilePathDataType::normalize($this->getFacade()->buildUrlToSource('LIBS.BOOTSTRAP_DATEPICKER.FOLDER'), DIRECTORY_SEPARATOR) .  DIRECTORY_SEPARATOR . 'locales' . DIRECTORY_SEPARATOR;
            
            $fullLocale = $this->getFacade()->getApp()->getTranslator()->getLocale();
            $locale = str_replace("_", "-", $fullLocale);
            if (file_exists($datepickerBasepath . 'bootstrap-datepicker.' . $locale . '.min.js')) {
                return ($this->bootstrapDatepickerLocale = $locale);
            }
            $locale = substr($fullLocale, 0, strpos($fullLocale, '_'));
            if (file_exists($datepickerBasepath . 'bootstrap-datepicker.' . $locale . '.min.js')) {
                return ($this->bootstrapDatepickerLocale = $locale);
            }
            
            $fallbackLocales = $this->getFacade()->getApp()->getTranslator()->getFallbackLocales();
            foreach ($fallbackLocales as $fallbackLocale) {
                $locale = str_replace("_", "-", $fallbackLocale);
                if (file_exists($datepickerBasepath . 'bootstrap-datepicker.' . $locale . '.min.js')) {
                    return ($this->bootstrapDatepickerLocale = $locale);
                }
                $locale = substr($fallbackLocale, 0, strpos($fallbackLocale, '_'));
                if (file_exists($datepickerBasepath . 'bootstrap-datepicker.' . $locale . '.min.js')) {
                    return ($this->bootstrapDatepickerLocale = $locale);
                }
            }
            
            $this->bootstrapDatepickerLocale = '';
        }
        return $this->bootstrapDatepickerLocale;
    }

    public function buildJsValueGetter()
    {
        return "( $('#{$this->getId()}').data('_internalValue') !== undefined ? $('#{$this->getId()}').data('_internalValue') : {$this->getDateFormatter()->buildJsFormatParser("$('#{$this->getId()}').val()")})";
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\AdminLTEFacade\Facades\Elements\LteInput::buildJsRequired()
     */
    function buildJsRequired()
    {
        $output = <<<JS

    function {$this->buildJsFunctionPrefix()}validate() {
        if ({$this->buildJsValidator()}) {
            $("#{$this->getId()}").parent().removeClass("invalid");
        } else {
            $("#{$this->getId()}").parent().addClass("invalid");
        }
    }
    
    // Bei leeren Werten, wird die toValue-Funktion und damit der Validator nicht aufgerufen.
    // Ueberprueft die Validitaet wenn das Element erzeugt wird.
    if (!$("#{$this->getId()}").val()) {
        $("#{$this->getId()}").data("_isValid", false);
        {$this->buildJsFunctionPrefix()}validate();
    }
    // Ueberprueft die Validitaet wenn das Element geaendert wird.
    $("#{$this->getId()}").on("input change", function() {
        if (!$("#{$this->getId()}").val()) {
            $("#{$this->getId()}").data("_isValid", false);
            {$this->buildJsFunctionPrefix()}validate();
        }
    });
JS;
        
        return $output;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\AdminLTEFacade\Facades\Elements\LteInput::buildJsValidator()
     */
    function buildJsValidator()
    {
        if ($this->isValidationRequired() === true && $this->getWidget()->isRequired()) {
            $output = '$("#' . $this->getId() . '").data("_isValid")';
        } else {
            $output = 'true';
        }
        
        return $output;
    }
}