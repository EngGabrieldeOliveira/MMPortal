<?php

namespace App\Enums;

enum AdministrativeEventType: string
{
    case Login = 'auth.login';
    case Logout = 'auth.logout';
    case LogoutAll = 'auth.logout_all';
    case LoginFailed = 'auth.login_failed';
    case InternalException = 'system.exception';
    case PermissionUpdated = 'security.permission_updated';
    case ConfigurationUpdated = 'system.configuration_updated';
    case DataExported = 'data.exported';
    case FileUploaded = 'file.upload';
}
