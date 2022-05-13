<?php

namespace Maduser\Laravel\Resource\Views;

use Maduser\Ui\Blade\Dashmix\Views\Table;
use Maduser\Laravel\ViewModel\ViewModel;

class ResourceDisplay extends ViewModel
{
    protected $view = 'resource.resource-display';

    /**
     * @var Table
     */
    protected $mainTable;

    /**
     * @var Table
     */
    protected $metaTable;

    /**
     * @return Table
     */
    public function getMainTable(): Table
    {
        return $this->mainTable;
    }

    /**
     * @param Table $mainTable
     *
     * @return ResourceDisplay
     */
    public function setMainTable(Table $mainTable): ResourceDisplay
    {
        $this->mainTable = $mainTable;

        return $this;
    }

    /**
     * @return Table
     */
    public function getMetaTable(): Table
    {
        return $this->metaTable;
    }

    /**
     * @param Table $metaTable
     *
     * @return ResourceDisplay
     */
    public function setMetaTable(Table $metaTable): ResourceDisplay
    {
        $this->metaTable = $metaTable;

        return $this;
    }

}
