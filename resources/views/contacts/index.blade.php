@extends('layouts.app')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <h3 class="fw-bold">Contacts</h3>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal" onclick="createContact()">
            <i class="bi bi-person-plus"></i> Add Contact
        </button>
        <a class="btn btn-secondary" href="{{ route('custom-fields.index') }}">
            <i class="bi bi-gear"></i> Custom Fields
        </a>
    </div>
</div>

<div class="card p-3 mb-3">
    <div class="row g-2">

        <div class="col-md-3">
            <input id="search" class="form-control filter-input" placeholder="Search Name or Email">
        </div>

        <div class="col-md-2">
            <select id="gender" class="form-select filter-input">
                <option value="">All Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="col-md-3">
            <select id="custom_field" class="form-select filter-input">
                <option value="">Filter by Custom Field</option>
                @foreach($customFields as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input id="custom_field_value" class="form-control filter-input d-none" placeholder="Custom field value">
        </div>

    </div>
</div>


<div id="contacts-table">
    @include('contacts.partials.table', ['contacts' => $contacts])
</div>

@include('contacts.partials.form')
@include('contacts.modals.merge')

@endsection

@push('scripts')
<script>
$(function () {

    function filterContacts() {
        $.ajax({
            url: "{{ route('contacts.filter') }}",
            type: "GET",
            data: {
                search: $("#search").val(),
                gender: $("#gender").val(),
                custom_field_id: $("#custom_field").val(),
                custom_value: $("#custom_field_value").val()
            },
            success: function(r){
                $("#contacts-table").html(r.html);
            }
        });
    }

    // Show/Hide custom value field depending on selection
    $("#custom_field").on("change", function() {
        if ($(this).val()) {
            $("#custom_field_value").removeClass("d-none").val("").focus();
        } else {
            $("#custom_field_value").addClass("d-none").val("");
        }
        filterContacts();
    });

    // Trigger filters
    $("#search, #gender").on("keyup change", filterContacts);
    $("#custom_field_value").on("keyup", filterContacts);

});
</script>
@endpush

