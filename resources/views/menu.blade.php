<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Μενού αρχικής σελίδας
        </h2>
    </x-slot>

    <div class="card" style="padding:20px; max-width:600px; margin:auto;">
        <h1>Μενού αρχικής σελίδας</h1>

        <ul>
            <li><a href="{{ route('documents.create') }}">Νέο εισερχόμενο</a></li>
            <li><a href="{{ route('documents.create') }}">Νέο εξερχόμενο</a></li>
            <li><a href="{{ route('incoming.index') }}">Φάκελος εισερχομένων</a></li>
            <li><a href="{{ route('outgoing.index') }}">Φάκελος εξερχομένων</a></li>
        </ul>
    </div>

</x-app-layout>
