<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingDocument extends Model
{
    protected $table = 'outgoing_documents';

    protected $fillable = [
        'aa',
        'protocol_number',
        'incoming_protocol',
        'incoming_date',
        'subject',
        'sender',
        'document_date',
        'incoming_document_number',
        'summary',
        'comments',

        // ✅ Απάντηση σε εισερχόμενο (nullable)
        'reply_to_incoming_id',
    ];

    public function replyToIncoming(): BelongsTo
    {
        return $this->belongsTo(IncomingDocument::class, 'reply_to_incoming_id');
    }
}
