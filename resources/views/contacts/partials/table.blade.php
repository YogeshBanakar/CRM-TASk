<table class="table table-bordered">
    <thead>
        <tr>
            <th>Sr.No</th>
            <th>Profile</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Custom Fields</th>
            <th>Files</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contacts as $contact)
            <tr>
                <td>{{ $loop->iteration }}</td>

                <td>
                    <img src="{{ $contact->profile_image ? asset('storage/'.$contact->profile_image) : asset('default-avatar.png') }}"
                        width="50" height="50" class="rounded-circle">
                </td>

                <td>{{ $contact->name }}</td>
                <td>{{ $contact->email }}</td>
                <td>{{ $contact->phone }}</td>
                <td>{{ ucfirst($contact->gender) }}</td>

                <td>
                    @foreach($contact->customValues as $cv)
                        @php
                            $val = json_decode($cv->value, true);
                            $display = is_array($val) ? implode(', ', $val) : $cv->value;
                        @endphp
                        <div><strong>{{ $cv->customField->name }}:</strong> {{ $display }}</div>
                    @endforeach

                </td>

                <td>
                    @if($contact->additional_file)
                        <a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/'.$contact->additional_file) }}" target="_blank">
                            View
                        </a>
                    @else
                        <span class="text-muted">No File</span>
                    @endif
                </td>

                <td>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-info btn-sm btn-responsive" onclick="editContact({{ $contact->id }})">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm btn-responsive" onclick="deleteContact({{ $contact->id }})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                        <button class="btn btn-warning btn-sm btn-responsive" onclick="initMerge({{ $contact->id }})">
                            <i class="bi bi-link"></i> Merge
                        </button>
                    </div>
                </td>


            </tr>
        @empty
            <tr><td colspan="9" class="text-center">No contacts found.</td></tr>
        @endforelse
    </tbody>
</table>