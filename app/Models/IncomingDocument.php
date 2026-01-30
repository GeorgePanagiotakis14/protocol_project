<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomingDocument extends Model
{
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
}
