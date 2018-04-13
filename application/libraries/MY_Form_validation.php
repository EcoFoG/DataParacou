<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Form_validation
 *
 * @author Elie.Guedj
 */
class MY_Form_validation extends CI_Form_validation {

    public function __construct($rules = array())
    {
        parent::__construct($rules);
    }


    public function valid_date($date)
    {
        $d = DateTime::createFromFormat('Y/m/d', $date);
        return $d && $d->format('Y/m/d') === $date;
    }
}