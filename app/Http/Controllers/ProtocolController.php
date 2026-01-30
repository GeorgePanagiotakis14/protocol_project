<?php
use App\Models\Protocol;
use Illuminate\Http\Request;

class ProtocolController extends Controller
{
    public function index()
    {
        $protocols = Protocol::latest()->get();
        return view('protocols.index', compact('protocols'));
    }

    public function store(Request $request)
    {
        $filePath = null;

        if ($request->hasFile('incoming_file')) {
            $filePath = $request->file('incoming_file')
                ->store('attachments', 'public');
        }

        Protocol::create([
            'incoming_date' => now()->toDateString(), // ΑΥΤΟΜΑΤΑ
            'incoming_sender' => $request->incoming_sender,
            'incoming_subject' => $request->incoming_subject,
            'incoming_description' => $request->incoming_description,
            'incoming_file' => $filePath,
        ]);

        return redirect()->back();
    }

    public function updateOutgoing(Request $request, $id)
    {
        $protocol = Protocol::findOrFail($id);

        $protocol->update([
            'outgoing_receiver' => $request->outgoing_receiver,
            'outgoing_description' => $request->outgoing_description,
            'outgoing_date' => now()->toDateString(),
        ]);

        return redirect()->back();
    }
}
