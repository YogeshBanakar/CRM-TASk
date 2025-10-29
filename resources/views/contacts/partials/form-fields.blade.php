@if (!isset($contact))
    @php $contact = new \App\Models\Contact(); @endphp
@endif

<input type="hidden" name="contact_id" id="contact_id" value="{{ $contact->id ?? '' }}">

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $contact->name ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Email *</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $contact->email ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $contact->phone ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Gender *</label><br>
        @php $gender = $contact->gender ?? 'male'; @endphp
        <input type="radio" name="gender" value="male" {{ $gender == 'male' ? 'checked' : '' }}> Male
        <input type="radio" name="gender" value="female" {{ $gender == 'female' ? 'checked' : '' }}> Female
        <input type="radio" name="gender" value="other" {{ $gender == 'other' ? 'checked' : '' }}> Other
    </div>
    <div class="col-md-6 mb-3">
        <label>Profile Image</label>
        <input type="file" name="profile_image" class="form-control">

        @if(!empty($contact->profile_image))
            <div class="mt-2">
                <img src="{{ asset('storage/' . $contact->profile_image) }}" 
                    alt="Profile Image" width="80" class="rounded">
            </div>
        @endif
    </div>
    <div class="col-md-6 mb-3">
        <label>Additional File</label>
        <input type="file" name="additional_file" class="form-control">
        @if(!empty($contact->additional_file))
            <div class="mt-2">
                <a href="{{ asset('storage/' . $contact->additional_file) }}" 
                target="_blank" class="btn btn-outline-secondary btn-sm">
                    View Existing File
                </a>
            </div>
        @endif
    </div>

</div>
@if($customFields->count() > 0)
    <hr>
    <h5>Custom Fields</h5>
    <div id="custom-fields">
        @foreach($customFields as $field)
            @php
                $value = $contact->customValues
                            ->where('custom_field_id', $field->id)
                            ->first()
                            ->value ?? '';
            @endphp
            <div class="mb-3">
                <label>{{ $field->name }}</label>
                @if($field->type === 'text')
                    <input type="text" name="custom_fields[{{ $field->id }}]" class="form-control" value="{{ $value }}">
                @elseif($field->type === 'date')
                    <input type="date" name="custom_fields[{{ $field->id }}]" class="form-control" value="{{ $value }}">
                @endif
            </div>
        @endforeach
    </div>
@endif
