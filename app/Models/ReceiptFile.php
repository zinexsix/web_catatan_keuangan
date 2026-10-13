<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Receipt image stored in the database (used when RECEIPT_DISK=database).
 */
#[Fillable(['path', 'mime_type', 'content'])]
class ReceiptFile extends Model
{
}
