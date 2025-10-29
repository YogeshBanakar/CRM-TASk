@extends('layouts.app')

@section('content')
    <div class="container">
       <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Custom Fields</h2>
            <a href="{{ url('/contacts') }}" class="btn btn-secondary">← Back to Contacts</a>
        </div>
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
                    <div id="dropdownOptions">
                        <input type="text" name="options[]" class="form-control mb-1" placeholder="Option 1">
                        <input type="text" name="options[]" class="form-control mb-1" placeholder="Option 2">
                    </div>
                    <button type="button" id="addOption" class="btn btn-sm btn-outline-primary mt-1">+ Add Option</button>
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
            
            function ensureBaseOptions() {
                const $container = $('#dropdownOptions');
                if ($container.find('input[name="options[]"]').length < 2) {
                    $container.empty();
                    $container.append(baseOptionHtml(1));
                    $container.append(baseOptionHtml(2));
                }
            }

            function baseOptionHtml(n) {
                return `<div class="input-group mb-2 base-option">
                            <input type="text" name="options[]" class="form-control" placeholder="Option ${n}">
                        </div>`;
            }

            // Renumber all option placeholders and handle remove buttons visibility
            function renumberOptions() {
                const $inputs = $('#dropdownOptions').find('input[name="options[]"]');
                $inputs.each(function(index) {
                    const number = index + 1;
                    $(this).attr('placeholder', `Option ${number}`);
                    // ensure parent div has correct classes
                    const $parent = $(this).closest('.input-group');
                    if (index < 2) {
                        // first two: remove any remove button and mark as base-option
                        $parent.removeClass('extra-option').addClass('base-option');
                        $parent.find('.remove-option').remove();
                    } else {
                        // extra ones: ensure remove button exists and mark as extra-option
                        $parent.removeClass('base-option').addClass('extra-option');
                        if ($parent.find('.remove-option').length === 0) {
                            $parent.append('<button type="button" class="btn btn-outline-danger remove-option">✕</button>');
                        }
                    }
                });
            }

            // Add new option
            $('#addOption').click(function() {
                // append new extra option (without using a global counter)
                $('#dropdownOptions').append(`
                    <div class="input-group mb-2 extra-option">
                        <input type="text" name="options[]" class="form-control" placeholder="Option">
                        <button type="button" class="btn btn-outline-danger remove-option">✕</button>
                    </div>
                `);
                renumberOptions();
            });

            // Remove an extra option
            $(document).on('click', '.remove-option', function() {
                $(this).closest('.input-group').remove();
                renumberOptions();
            });

            // init
            ensureBaseOptions();
            renumberOptions();
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