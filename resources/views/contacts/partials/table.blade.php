<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
             <th>Profile</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>File</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contacts as $index => $contact)
        <tr>
            <td>{{ $contact->id }}</td>
            <td>
                @if($contact->profile_image)
                    <img src="{{ asset('storage/' . $contact->profile_image) }}" 
                         alt="Profile" width="50" height="50" class="rounded-circle">
                @else
                    <img src="{{ asset('default-avatar.png') }}" 
                         alt="Default" width="50" height="50" class="rounded-circle">
                @endif
            </td>
            <td>{{ $contact->name }}</td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->phone }}</td>
            <td>{{ ucfirst($contact->gender) }}</td>
            <td>
                @if($contact->additional_file)
                    <a href="{{ asset('storage/' . $contact->additional_file) }}" 
                       target="_blank" class="btn btn-sm btn-outline-primary">
                        View File
                    </a>
                @else
                    <span class="text-muted">No File</span>
                @endif
            </td>
            <td>
                <button class="btn btn-sm btn-info" onclick="editContact({{ $contact->id }})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteContact({{ $contact->id }})">Delete</button>
                <button class="btn btn-sm btn-warning" onclick="initMerge({{ $contact->id }})">Merge</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No contacts found.</td></tr>
        @endforelse
    </tbody>
</table>