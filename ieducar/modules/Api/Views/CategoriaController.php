<?php

class CategoriaController extends ApiCoreController
{
    protected function getCategorias()
    {
        $obj = new clsPmieducarCategoriaAcervo();
        $arrayCategorias;

        foreach ($obj->listaCategoriasPorObra($this->getRequest()->id) as $reg) {
            $arrayCategorias[] = $reg['categoria_id'];
        }

        return ['categorias' => $arrayCategorias];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'categoria-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'categorias')) {
            $this->appendResponse($this->getCategorias());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
