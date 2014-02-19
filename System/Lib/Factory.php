<?php
class System_Lib_Factory
{

	protected $poll = array();

    /**
     *
     * @param <string> $name
     * @return <obj>
     */
    public function get($name)
    {
        if (!isset($this->poll[$name]))
        {
            $this->poll[$name] = $this->create($name);
        }
        return $this->poll[$name];
    }

    /**
     *
     * @param <string> $name
     * @return <obj>
     */
    protected function create($name)
    {
        return new $name;
    }
}