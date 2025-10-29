@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Manage Custom Fields</h2>

        <form id="addFieldForm" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Field Name (e.g., Birthday)" required>
                </div>
                <div class="col-md-3">
                    <select name="type" id="fieldType" class="form-control" required>
                        <option value="text">Text</option>
                        <option value="date">Date</option>
                        <option value="dropdown">Dropdown</option>
                    </select>
                </div>
                <div class="col-md-3" id="optionsGroup" style="display:none;">
                    <input type="text" name="options[]" class="form-control mb-1" placeholder="Option 1">
                    <input type="text" name="options[]" class="form-control" placeholder="Option 2">
                    <small class="text-muted">Add more with JS if needed</small>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Add Field</button>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="fieldsList">
                @foreach($fields as $field)
                <tr>
                    <td>{{ $field->name }}</td>
                    <td>{{ ucfirst($field->type) }}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="deleteField({{ $field->id }})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {

            // Toggle dropdown options
            $('#fieldType').change(function() {
                $('#optionsGroup').toggle($(this).val() === 'dropdown');
            });

            // Add new field
            $('#addFieldForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('custom-fields.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Added!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong!'
                        });
                    }
                });
            });

            // Delete field
            window.deleteField = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This field will be deleted permanently!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/custom-fields/${id}`,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 1200,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            },
                            error: function() {
                                Swal.fire('Error', 'Unable to delete field.', 'error');
                            }
                        });
                    }
                });
            };
        });
    </script>
@endsection