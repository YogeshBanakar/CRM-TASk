@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Manage Custom Fields</h3>
            <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Contacts
            </a>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <form id="addFieldForm">
                @csrf
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Field Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Birthday" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Field Type *</label>
                        <select name="type" id="fieldType" class="form-select" required>
                            <option value="text">Text</option>
                            <option value="date">Date</option>
                            <option value="dropdown">Dropdown</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="optionsGroup" style="display:none;">
                        <label class="form-label">Options</label>

                        <div id="dropdownOptions">
                            <div class="input-group mb-2">
                                <input type="text" name="options[]" class="form-control" placeholder="Option 1">
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" name="options[]" class="form-control" placeholder="Option 2">
                            </div>
                        </div>

                        <button type="button" id="addOption" class="btn btn-sm btn-outline-primary mt-1">
                            + Add Option
                        </button>
                    </div>

                    <div class="col-md-1 d-flex align-items-start">
                        <button type="submit" class="btn btn-success w-100 mt-4">Add</button>
                    </div>
                </div>

            </form>
        </div>

        <div class="card shadow-sm p-4">
            <h5 class="mb-3">Existing Custom Fields</h5>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>
                <tbody id="fieldsList">
                    @forelse($fields as $field)
                    <tr>
                        <td>{{ $field->name }}</td>
                        <td>{{ ucfirst($field->type) }}</td>
                        <td>
                            <button class="btn btn-sm btn-danger w-100" onclick="deleteField({{ $field->id }})">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">No custom fields created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(function () {

            // Show/hide options for dropdown or checkbox
            $('#fieldType').on('change', function() {
                $('#optionsGroup').toggle($(this).val() === 'dropdown' || $(this).val() === 'checkbox');
            });

            // Add option input dynamically
            $('#addOption').on('click', function() {
                $('#dropdownOptions').append(`
                    <div class="input-group mb-2">
                        <input type="text" name="options[]" class="form-control" placeholder="Option">
                        <button type="button" class="btn btn-outline-danger remove-option">✕</button>
                    </div>
                `);
            });

            // Remove option
            $(document).on('click', '.remove-option', function() {
                $(this).closest('.input-group').remove();
            });

            // Save Field
            $('#addFieldForm').on('submit', function(e) {
                e.preventDefault();
                $.post("{{ route('custom-fields.store') }}", $(this).serialize(), function(res){
                    Swal.fire({ icon:'success', title:'Added', text:res.message, timer:1500, showConfirmButton:false })
                        .then(() => location.reload());
                }).fail(err => {
                    Swal.fire('Error', err.responseJSON.message ?? 'Something went wrong', 'error');
                });
            });

        });

        // Delete Field
        function deleteField(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/custom-fields/${id}`,
                        type: "DELETE",
                        data: { _token: '{{ csrf_token() }}' },
                        success: res => {
                            Swal.fire('Deleted!', res.message, 'success').then(() => location.reload());
                        }
                    });
                }
            });
        }
    </script>
@endpush
