<?php

declare(strict_types=1);

namespace App\Enums;

enum FormOperations: string
{
    case CREATE = 'create';
    case EDIT = 'edit';
    case VIEW = 'view';

}
