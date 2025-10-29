<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contacts as $contact)
        <tr>
            <td>{{ $contact->id }}</td>
            <td>{{ $contact->name }}</td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->phone }}</td>
            <td>{{ ucfirst($contact->gender) }}</td>
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