<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 25/10/13
 * Time: 11:08
 * To change this template use File | Settings | File Templates.
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Entity\Helpers;


class UidGenerator
{

    /**
     * Returns a (version 4) GUID
     */
    static function uid()
    {
        if (function_exists('com_create_guid') === true) {
            return strtolower(trim(com_create_guid(), '{}'));
        }


        return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
    }

}