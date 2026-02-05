<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class IncomingDocument extends Model
{
    use HasFactory;
    protected $table = 'incoming_documents';

    protected $fillable = [
        'aa',
        'protocol_number',
        'incoming_protocol',
        'incoming_date',
        'subject',
        'sender',
        'document_date',
        'summary',
        'comments',
        'attachment_path',
    ];

    public function outgoingReplies(): HasMany
    {
        return $this->hasMany(OutgoingDocument::class, 'reply_to_incoming_id');
    }

    public function attachments()
    {
        return $this->hasMany(\App\Models\IncomingDocumentAttachment::class, 'incoming_document_id');
    }

}


