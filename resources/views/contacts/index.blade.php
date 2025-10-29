@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Contacts</h2>

    <div class="mb-3">
        <input type="text" id="search" class="form-control d-inline w-50" placeholder="Search by name or email...">
        <select id="gender" class="form-control d-inline w-25 ms-2">
            <option value="">All Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#contactModal" onclick="createContact()">Add Contact</button>
    </div>

    <div id="contacts-table">
       @include('contacts.partials.table', ['contacts' => $contacts ?? collect()])
    </div>
</div>

@include('contacts.partials.form')
@include('contacts.modals.merge')
@endsection