<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutgoingDocumentAttachment extends Model
{
    protected $fillable = [
        'outgoing_document_id',
        'path',
        'original_name',
        'size',
    ];

    public function document()
    {
        return $this->belongsTo(OutgoingDocument::class, 'outgoing_document_id');
    }
}
