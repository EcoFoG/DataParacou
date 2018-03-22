<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of email
 *
 * @author Elie.Guedj
 */
class PeEmail {
    
    protected $CI;
    
    function __construct(){
            $this->CI =& get_instance();
            $this->CI->load->library('email');
            $this->CI->config->load("email");
        }
    function sendMail($dest,$subject,$message){
        $mail_config = $this->CI->config->item("email");
        $this->CI->email->initialize( $mail_config );

        $this->CI->email->clear();

        $this->CI->email->from( $mail_config["smtp_user"] );
        $this->CI->email->to( $dest );

        $this->CI->email->subject( $subject );
        $this->CI->email->message( $message );
        if ($this->CI->email->send()) {
            return true;
        } else {
            return $this->CI->email->print_debugger();
        }
    }
}
