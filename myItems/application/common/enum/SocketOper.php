<?php
/**
 * Created by PhpStorm.
 * User: wkk
 * Date: 12/21/18
 * Time: 10:18 PM
 */

namespace app\common\enum;


class SocketOper
{
    CONST GET_APPOINT = 4;
    CONST INIT_APPOINT = 5;

    CONST SUBMIT_TEMP_APPOINT = 6;
    CONST APPOINTED_TEMP_SUCCESS = 7;
    CONST APPOINTED_TEMP_FAILED = 8;
    CONST APPOINTED_TEMP_EXPIRED = 9;

    CONST APPOINTED_TEMP_DEL = 10;

}