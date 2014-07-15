<?php
/**
 * Validate profiles
 * @param String $profile
 * @return bool true/false
 */
function validate($strProfiles = '')
{
    $user = App_edvSecurity::getInstance();
    if ($user->isLogged())
    {

        if(false === empty($strPerfiles))
        {
            $arrayProfiles = explode(',', $strProfiles);
            #if profile is incorrect denied access
            if (false === in_array($user->userLoggued()->profile_id , $arrayProfiles))
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
 * Return user loggued information
 * @returnc Object recordset
 */
function userdata()
{
    $user = App_edvSecurity::getInstance();
    return $user->userLoggued();
}


