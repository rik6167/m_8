<?php

/**
 * Valida si el usuario tiene el perfil que es
 * @param String $perfiles perfiles uno solo o separado por comas
 * @return bool true/false
 */
function validate($strPerfiles = '')
{
    $user = App_edvSecurity::getInstance();
    if ($user->isLogged())
    {

        if(false === empty($strPerfiles))
        {
            $arrayPerfiles = explode(',', $strPerfiles);
            #Si el rol de la persona no concuerda con el pasado, niega el acceso
            if (false === in_array($user->userLoggued()->perfil_id , $arrayPerfiles))
            {
                return false;
            }

        }
        return true;

    } else {
        return false;
    }
}

/**
 * Devuelve la informacion del usuario logueado
 * @returnc Object recordset
 */
function userdata()
{
    $user = App_edvSecurity::getInstance();
    return $user->userLoggued();
}


