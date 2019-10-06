<?php

declare(strict_types=1);

namespace App\Error;

final class ApiError
{
    public const ENTITY_VALIDATION_ERROR = 'entity_validation_error';
    public const ENTITY_LIST_ERROR       = 'entity_list_error';
    public const ENTITY_UUID_ERROR       = 'entity_uuid_error';
    public const ENTITY_CREATE_ERROR     = 'entity_create_error';
    public const ENTITY_READ_ERROR       = 'entity_read_error';
    public const ENTITY_UPDATE_ERROR     = 'entity_update_error';
    public const ENTITY_DELETE_ERROR     = 'entity_delete_error';
}
