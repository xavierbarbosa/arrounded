<?php
namespace Arrounded\Services\Statistics;

use Arrounded\Collection;

class Chart
{
    /**
     * The element the graphic is for
     *
     * @type string
     */
    protected $element;

    /**
     * The type of the graphic
     *
     * @type string
     */
    protected $type = 'Line';

    /**
     * The datasets
     *
     * @type array
     */
    protected $datasets = array();

    /**
     * The graphics options
     *
     * @type array
     */
    protected $options = array();

    /**
     * The color scheme to use
     *
     * @type array
     */
    protected $colors = ['#16A085', '#2980B9', '#8E44AD', '#F1C40F', '#E67E22', '#C0392B', '#BDC3C7'];

    /**
     * The various labels
     *
     * @type array
     */
    protected $labels = array();

    /**
     * Magic metod for constructor
     *
     * @param string $type
     * @param string $element
     *
     * @return self
     */
    public static function make($type, $element)
    {
        $chart = new static();
        $chart->setType($type);
        $chart->setElement($element);

        return $chart;
    }

    /**
     * Render on string cast
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////// GETTERS AND SETTERS //////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Set the color scheme to use
     *
     * @param array $colors
     *
     * @return $this
     */
    public function setColors(array $colors)
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * Sets the element the graphic is for
     *
     * @param string $element the element
     *
     * @return self
     */
    public function setElement($element)
    {
        $this->element = 'statistic--'.$element;

        return $this;
    }

    /**
     * Sets the type of the graphic
     *
     * @param string $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Sets the datasets
     *
     * @param array|Collection $datasets the datasets
     *
     * @return self
     */
    public function setDatasets($datasets)
    {
        $this->datasets = $this->formatDatasets($datasets);

        return $this;
    }

    /**
     * Sets the graphics options
     *
     * @param array $options the options
     *
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Sets the various labels
     *
     * @param array $labels the labels
     *
     * @return self
     */
    public function setLabels(array $labels)
    {
        $this->labels = $this->formatLabels($labels);

        return $this;
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// RENDERING ///////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Render the chart
     *
     * @return string
     */
    public function render()
    {
        $template = 'new Chart(document.getElementById("%s").getContext("2d")).%s(%s, %s);';
        $template = sprintf(
            $template,
            $this->element,
            $this->type,
            json_encode($this->datasets),
            json_encode($this->options)
        );

        return $template;
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////////// HELPERS ////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Fill holes in labels
     *
     * @param array $labels
     *
     * @return array
     */
    protected function formatLabels(array $labels)
    {
        foreach ($labels as &$label) {
            $label = $label ?: 'N/A';
        }

        return $labels;
    }

    /**
     * Format datasets
     *
     * @param array|Collection $datasets
     *
     * @return array
     */
    protected function formatDatasets($datasets)
    {
        $data = [];

        switch ($this->type) {

            case 'Pie':
            case 'Doughnut':
                $datasets = is_array($datasets[0]) ? $datasets[0] : $datasets;
                foreach ($datasets as $key => $value) {
                    $data[] = array(
                        'label' => array_get($this->labels, $key),
                        'value' => $value,
                        'color' => array_get($this->colors, $key),
                    );
                }
                break;

            case 'Bar':
                $data['labels']   = $this->labels;
                $data['datasets'] = array();
                foreach ($datasets as $key => $value) {
                    $data['datasets'][] = array(
                        'fillColor'   => array_get($this->colors, $key),
                        'strokeColor' => array_get($this->colors, $key),
                        'data'        => $value,
                    );
                }
                break;
        }

        return $data;
    }
}
