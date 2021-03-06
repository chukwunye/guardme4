<?php

namespace Backpack\CRUD\PanelTraits;

use Illuminate\Http\Request;

trait Filters
{
    // ------------
    // FILTERS
    // ------------

    public $filters = [];

    public function __construct()
    {
        $this->filters = new FiltersCollection;
    }

    /**
     * Add a filter to the CRUD table view.
     *
     * @param array         $options        Name, type, label, etc.
     * @param array/closure $values         The HTML for the filter.
     * @param closure       $filter_logic   Query modification (filtering) logic.
     */
    public function addFilter($options, $values = false, $filter_logic = false)
    {
        // if a closure was passed as "values"
        if (is_callable($values)) {
            // get its results
            $values = $values();
        }

        // check if another filter with the same name exists
        if (! isset($options['name'])) {
            abort(500, 'All your filters need names.');
        }
        if ($this->filters->contains('name', $options['name'])) {
            abort(500, "Sorry, you can't have two filters with the same name.");
        }

        // add a new filter to the interface
        $filter = new CrudFilter($options, $values, $filter_logic);
        $this->filters->push($filter);

        // if a closure was passed as "filter_logic"
        if ($this->doingListOperation() &&
            $this->request->input($options['name']) &&
            $this->request->input($options['name']) != null &&
            $this->request->input($options['name']) != 'null') {
            if (is_callable($filter_logic)) {
                // apply it
                $filter_logic($this->request->input($options['name']));
            } else {
                $this->addDefaultFilterLogic($filter->name, $filter_logic);
            }
        }
    }

    public function addDefaultFilterLogic($name, $operator)
    {
        $input = \Request::all();

        // if this filter is active (the URL has it as a GET parameter)
        switch ($operator) {
            // if no operator was passed, just use the equals operator
            case false:
                $this->addClause('where', $name, $input[$name]);
                break;

            case 'scope':
                $this->addClause($operator);
                break;

            // TODO:
            // whereBetween
            // whereNotBetween
            // whereIn
            // whereNotIn
            // whereNull
            // whereNotNull
            // whereDate
            // whereMonth
            // whereDay
            // whereYear
            // whereColumn
            // like

            // sql comparison operators
            case '=':
            case '<=>':
            case '<>':
            case '!=':
            case '>':
            case '>=':
            case '<':
            case '<=':
                $this->addClause('where', $name, $operator, $input[$name]);
                break;

            default:
                abort(500, 'Unknown filter operator.');
                break;
        }
    }

    public function filters()
    {
        return $this->filters;
    }

    public function removeFilter($name)
    {
        $this->filters = $this->filters->reject(function ($filter) use ($name) {
            return $filter->name == $name;
        });
    }

    public function removeAllFilters()
    {
        $this->filters = collect([]);
    }

    /**
     * Determine if the current CRUD action is a list operation (using standard or ajax DataTables).
     * @return bool
     */
    public function doingListOperation()
    {
        $route = $this->route;

        switch ($this->request->url()) {
            case url($this->route):
                if ($this->request->getMethod() == 'POST' ||
                    $this->request->getMethod() == 'PATCH') {
                    return false;
                }
                return true;
                break;

            case url($this->route.'/search'):
                return true;
                break;

            default:
                return false;
                break;
        }
    }
}

class FiltersCollection extends \Illuminate\Support\Collection
{
    public function removeFilter($name)
    {
    }

    // public function count()
    // {
    //     dd($this);
    // }
}

class CrudFilter
{
    public $name; // the name of the filtered variable (db column name)
    public $type = 'select'; // the name of the filter view that will be loaded
    public $label;
    public $placeholder;
    public $values;
    public $options;
    public $currentValue;
    public $view;

    public function __construct($options, $values, $filter_logic)
    {
        $this->checkOptionsIntegrity($options);

        $this->name = $options['name'];
        $this->type = $options['type'];
        $this->label = $options['label'];

        if (! isset($options['placeholder'])) {
            $this->placeholder = '';
        } else {
            $this->placeholder = $options['placeholder'];
        }

        $this->values = $values;
        $this->options = $options;
        $this->view = 'crud::filters.'.$this->type;

        if (\Request::input($this->name)) {
            $this->currentValue = \Request::input($this->name);
        }
    }

    public function checkOptionsIntegrity($options)
    {
        if (! isset($options['name'])) {
            abort(500, 'Please make sure all your filters have names.');
        }
        if (! isset($options['type'])) {
            abort(500, 'Please make sure all your filters have types.');
        }
        if (! \View::exists('crud::filters.'.$options['type'])) {
            abort(500, 'No filter view named "'.$options['type'].'.blade.php" was found.');
        }
        if (! isset($options['label'])) {
            abort(500, 'Please make sure all your filters have labels.');
        }
    }

    public function isActive()
    {
        if (\Request::input($this->name)) {
            return true;
        }

        return false;
    }
}
