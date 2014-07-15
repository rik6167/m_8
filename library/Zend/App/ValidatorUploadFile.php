<?php
class  App_ValidatorUploadFile {

    private $log;


    public function __construct() {



    }

    public function __destruct() {

    }

    public function validateString($cadena, $num_linea) {
        
        if(!empty($cadena)) {
            if(!is_string($cadena)) {
               $this->log[] = "linea ".$num_linea." : registro inválido";
                $num_linea++;
                return false ;
            }
        }

        return true;
    }

    public function valitateNumber($cadena, $num_linea){

        if(!empty($cadena)) {
            if(!is_numeric($cadena)) {
               $this->log[] = "linea ".$num_linea." : registro inválido";
                $num_linea++;
                return false ;
            }else{
                return true;
            }
        }
        return true;

        
        }


    public function validateDateFormat(){




    }

    public function getErrorMessages(){



    }





}
?>