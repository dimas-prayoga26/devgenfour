<?php

namespace App\Exceptions;

use Exception;

/**
 * Raised when the translation service cannot return a translation; the message is safe to show to admins.
 */
class TranslationFailed extends Exception
{
    //
}
