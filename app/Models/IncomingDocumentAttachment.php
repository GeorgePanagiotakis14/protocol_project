<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingDocumentAttachment extends Model
{
    protected $fillable = [
        'incoming_document_id',
        'path',
        'original_name',
        'size',
    ];

    public function document()
    {
        return $this->belongsTo(IncomingDocument::class, 'incoming_document_id');
    }
}
