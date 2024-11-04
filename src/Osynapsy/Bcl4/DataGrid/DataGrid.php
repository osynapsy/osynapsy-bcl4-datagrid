<?php

/*
 * This file is part of the Osynapsy package.
 *
 * (c) Pietro Celeste <p.celeste@osynapsy.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osynapsy\Bcl4\DataGrid;

use Osynapsy\Html\Component\AbstractComponent;
use Osynapsy\Bcl4\IPagination;
use Osynapsy\Bcl4\Pagination\Pagination;

class DataGrid extends AbstractComponent
{
    const BORDER_FULL = 'full';
    const BORDER_HORIZONTAL = 'horizontal';

    private $columns = [];
    private $emptyMessage = 'No data found';
    private $pagination;
    private $showHeader = true;
    private $title;
    private $rowWidth = 12;
    private $rowMinimum = 0;
    private $showExecutionTime = false;
    private $totalFunction;
    protected $totals = [];

    public function __construct($name)
    {
        parent::__construct('div', $name);
        $this->requireCss('bcl4/datagrid/style.css');
        $this->requireJs('bcl4/datagrid/script.js');
        $this->addClass('bcl-datagrid');
    }

    /**
     * Internal method to build component
     */
    public function preBuild()
    {
        $this->add(DataGridBuilder::build($this));
    }

    /**
     * Add a data column view
     *
     * @param type $label of column (show)
     * @param type $field name of array data field to show
     * @param type $class css to apply column
     * @param type $type type of data (necessary for formatting value)
     * @param callable $function for manipulate data value
     * @return $this
     */
    public function addColumn($label, $field, $class = '', $type = 'string', callable $function = null, $fieldOrderBy = null)
    {
        if (is_callable($field)) {
            $function = $field;
            $field = '';
        } elseif ($type !== 'date' && is_callable($type)) {
            $function = $type;
            $type = 'string';
        }
        $this->columns[$label] = new DataGridColumn($label, $field, $class, $type, $function, $fieldOrderBy);
        $this->columns[$label]->setParent($this->id);
        return $this->columns[$label];
    }

    /**
     * Get column by label
     *
     * @param string $label
     * @return Column
     */
    public function getColumn($label)
    {
        return $this->columns[$label];
    }

    /**
     * Get all columns
     *
     * @param void
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get empty message
     *
     * @param void
     * @return string
     */
    public function getEmptyMessage()
    {
        return $this->emptyMessage;
    }

    /**
     * return pager object
     *
     * @return Pagination object
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Get title of grid
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Hide Header
     *
     * @return $this;
     */
    public function hideHeader()
    {
        $this->showHeader = false;
        return $this;
    }

    /**
     * Remove column from repo of columns
     *
     * @param string $label
     */
    public function removeColumn($label)
    {
        if (array_key_exists($label, $this->columns)) {
            unset($this->columns[$label]);
        }
    }

    /**
     * Set array of columns rule
     *
     * @param type $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Set message to show when no data found.
     *
     * @param type $message
     * @return $this
     */
    public function setEmptyMessage($message)
    {
        $this->emptyMessage = $message;
        return $this;
    }

    public function setRowMinimum($min)
    {
        $this->rowMinimum = $min;
    }

    /**
     * Set width of row in bootstrap unit grid (max width = 12)
     *
     * @param int $width
     */
    public function setRowWidth($width)
    {
        $this->rowWidth = $width;
        return $this;
    }

    /**
     * Set a pagination object
     *      *
     * @param type $db Handler db connection
     * @param string $sqlQuery Sql query
     * @param array $sqlParameters Parameters of sql query
     * @param integer $pageDimension Page dimension (in row)
     */
    public function setPagination($db, $sqlQuery, $sqlParameters, $pageDimension = 10, $showPageDimension = true, $showPageInfo = true, $showExecutionTime = false)
    {
        $paginationId = $this->id.(strpos($this->id, '_') ? '_pagination' : 'Pagination');
        $this->pagination = new Pagination($paginationId, empty($pageDimension) ? 10 : $pageDimension, $showPageDimension, $showPageInfo);
        $this->pagination->setSql($db, $sqlQuery, $sqlParameters);
        $this->pagination->setParentComponent($this->id);       
        $this->showExecutionTime = $showExecutionTime;
        return $this->pagination;
    }

    public function setPaginator(IPagination $paginator, $showPageDimension = true, $showPageInfo = true)
    {
        $this->pagination = $paginator;
        $this->pagination->setParentComponent($this->id);
        $this->showPaginationPageDimension = $showPageDimension;
        $this->showPaginationPageInfo = $showPageInfo;
        return $this->pagination;
    }

    /**
     * Method for set table and rows borders visible
     *
     * return void;
     */
    public function setBorderOn($borderType = 'horizontal')
    {
        $this->addClass(sprintf('bcl-datagrid-border-on bcl-datagrid-border-on-%s', $borderType));
    }

    /**
     * Set title to show on top of datagrid
     *
     * @param type $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setTotalFunction(callable $function)
    {
        $this->totalFunction = $function;
    }

    /**
     * Returns the value of $showHeader, indicating whether to show the table header.
     *
     * @return bool
     */
    public function showHeader()
    {
        return $this->showHeader;
    }

    /**
     * Returns the value of showExecutionTime, indicating whether to show the execution time
     *
     * @return bool
     */
    public function showExecutionTime()
    {
        return $this->showExecutionTime;
    }
}
