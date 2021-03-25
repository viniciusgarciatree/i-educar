<?php

class Portabilis_View_Helper_Input_Resource_MultipleSearchAjaxAreasConhecimento extends Portabilis_View_Helper_Input_MultipleSearchAjax
{
    public function multipleSearchAjaxAreasConhecimento($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'areasconhecimento',
            'apiController' => 'AreaConhecimentoController',
            'apiResource' => 'areaconhecimento-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::multipleSearchAjax($options['objectName'], $attrName, $options);
    }
}
