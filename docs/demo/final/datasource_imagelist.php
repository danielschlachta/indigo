<?php

class idg_datasource_imagelist extends idg_datasource_instance
{   
    function __construct(&$parameters)
    {
        parent::__construct($parameters);
        
        $path = @$this->parameters['path'];
        
        if (!$path)
            idg_diag($this, get_class($this) 
                . ': mandatory parameter(path) not set');

        $directory = scandir($path);
                                                 
        for ($i = 0; $i < count($directory); $i ++) {       
                
            if (in_array($directory[$i], array('.', '..')))
                continue;
            
            $filename = $path . '/' . $directory[$i];
            $token = new idg_token($filename); // only way to set data
            
            $properties = array(
                'type' => pathinfo($filename, PATHINFO_EXTENSION)
            );
            
            $token->set_properties($properties);

            $this->tokens[] = $token;
        }
    }
}

?>
