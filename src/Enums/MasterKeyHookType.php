<?php

namespace Mariojgt\MasterKey\Enums;

enum MasterKeyHookType: string
{
    case BEFORE_REQUEST_CODE = 'before_request_code';
    case AFTER_REQUEST_CODE = 'after_request_code';
    case BEFORE_VERIFY = 'before_verify';
    case CREATE_USER = 'create_user';
    case AFTER_VERIFY = 'after_verify';
    case BEFORE_WEB_LOGIN = 'before_web_login';
    case AFTER_WEB_LOGIN = 'after_web_login';
    case BEFORE_APPROVE = 'before_approve';
    case AFTER_APPROVE = 'after_approve';
}
