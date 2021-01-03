<?php

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
    return $this->loadExtension('dingtalk')->isOpenMethod($module, $method);
}

