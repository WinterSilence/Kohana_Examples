<?php
/**
 * Test class
 */
class Test extends Base_Dynamic
{
    private $_var_1 = 5;
    private $_var_2 = array(1, 2, 5);

    protected $_pvar3;
    protected $_pvar4s = 'gfgf';
    
    private $_var5 = 5;

    public $free;
    
    public function set_pvar4s($value)
    {
        $this->_pvar4s = $value.' - Profit!';
        
        return $this;
    }

    public function get_var_1()
    {
        return $this->_var_1;
    }

    public function set_var_1($value)
    {
        $this->_var_1 = (string) $value;
        
        return $this;
    }

    public function pvar3($value = NULL)
    {
        if (isset($value))
        {
            $this->_pvar3 = (int) $value;
            return $this;
        }
        return $this->_pvar3;
    }
}
