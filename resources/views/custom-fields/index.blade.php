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

<script>
$('#fieldType').change(function() {
    $('#optionsGroup').toggle($(this).val() === 'dropdown');
});

$('#addFieldForm').submit(function(e) {
    e.preventDefault();
    $.post('/custom-fields', $(this).serialize(), function() {
        location.reload();
    });
});

function deleteField(id) {
    if (confirm('Delete this field?')) {
        $.ajax({
            url: `/custom-fields/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: () => location.reload()
        });
    }
}
</script>
@endsection