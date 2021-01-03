<?php

class dingtalkCommon extends commonModel
{
    /**
     * Juage a method of one module is open or not?
     *
     * @param  string $module
     * @param  string $method
     * @access public
     * @return bool
     */
    public function isOpenMethod($module, $method)
    {
        if($module == 'dingtalk' and $method == 'login')  return true;
        return parent::isOpenMethod($module, $method);
    }
}