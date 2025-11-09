@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Contacts</h2>

        <div class="mb-3 d-flex gap-2 flex-wrap">
            <input type="text" id="search" class="form-control w-25" placeholder="Search by name or email">
            <select id="gender" class="form-control w-25">
                <option value="">All Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <select id="custom_field" class="form-control w-25">
                <option value="">Filter by Custom Field</option>
                @foreach(\App\Models\CustomField::all() as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>

            <input type="text" id="custom_field_value" class="form-control w-25" placeholder="Custom field value">

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal" onclick="createContact()">Add Contact</button>
            <a class="btn btn-secondary" href="{{ route('custom-fields.index') }}">Manage Custom Fields</a>
        </div>

        <div id="contacts-table">
        @include('contacts.partials.table', ['contacts' => $contacts ?? collect()])
        </div>
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
                        success: function(response) {
                            $("#contacts-table").html(response.html);
                        }
                    });
                }
            
                $("#search, #gender, #custom_field, #custom_field_value").on("keyup change", function(){
                    filterContacts();
                });
            });
        </script>
    @endpush