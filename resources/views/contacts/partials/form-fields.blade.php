@if (!isset($contact))
    @php $contact = new \App\Models\Contact(); @endphp
@endif

<input type="hidden" name="contact_id" id="contact_id" value="{{ $contact->id ?? '' }}">

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Name *</label>
        <input type="text" name="name" class="form-control" value="{{ $contact->name ?? '' }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Email *</label>
        <input type="email" name="email" class="form-control" value="{{ $contact->email ?? '' }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ $contact->phone ?? '' }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Gender *</label><br>
        @php $gender = $contact->gender ?? 'male'; @endphp
        <label><input type="radio" name="gender" value="male" {{ $gender == 'male' ? 'checked' : '' }}> Male</label>
        <label class="ms-2"><input type="radio" name="gender" value="female" {{ $gender == 'female' ? 'checked' : '' }}> Female</label>
        <label class="ms-2"><input type="radio" name="gender" value="other" {{ $gender == 'other' ? 'checked' : '' }}> Other</label>
    </div>
    <div class="col-md-6 mb-3">
        <label>Profile Image</label>
        <input type="file" name="profile_image" class="form-control">
        @if(!empty($contact->profile_image))
            <img src="{{ asset('storage/' . $contact->profile_image) }}" width="80" class="mt-2 rounded">
        @endif
    </div>

    <div class="col-md-6 mb-3">
        <label>Additional File</label>
        <input type="file" name="additional_file" class="form-control">
        @if(!empty($contact->additional_file))
            <a href="{{ asset('storage/' . $contact->additional_file) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">View File</a>
        @endif
    </div>
</div>

@if($customFields->count() > 0)
    <hr>
    <h5>Custom Fields</h5>

    @foreach($customFields as $field)
        @php
            $record = $contact->customValues->firstWhere('custom_field_id', $field->id);
            $value = $record ? $record->value : '';
            $options = is_array($field->options) ? $field->options : (json_decode($field->options, true) ?? []);
            if ($field->type === 'checkbox') {
                $selectedValues = json_decode($value, true);
                if (!is_array($selectedValues)) $selectedValues = [];
            } else {
                $selectedValues = $value;
            }
        @endphp

        <div class="mb-3">
            <label>{{ $field->name }}</label>
            @if($field->type === 'text')
                <input type="text" name="custom_fields[{{ $field->id }}]" class="form-control" value="{{ $value }}">
            @elseif($field->type === 'date')
                <input type="date" name="custom_fields[{{ $field->id }}]" class="form-control" value="{{ $value }}">
            @elseif($field->type === 'dropdown')
                <select name="custom_fields[{{ $field->id }}]" class="form-select">
                    <option value="">-- Select --</option>
                    @foreach($options as $option)
                        <option value="{{ $option }}" {{ $option == $value ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            @elseif($field->type === 'checkbox')
                @foreach($options as $option)
                    @php $checkboxId = 'cf_'.$field->id.'_'.str_replace(' ', '_', $option); @endphp
                    <div class="form-check">
                        <input
                            type="checkbox"
                            id="{{ $checkboxId }}"
                            class="form-check-input"
                            name="custom_fields[{{ $field->id }}][]"
                            value="{{ $option }}"
                            {{ in_array($option, $selectedValues) ? 'checked' : '' }}
                        >
                        <label for="{{ $checkboxId }}" class="form-check-label" style="cursor:pointer;">
                            {{ $option }}
                        </label>
                    </div>
                @endforeach
            @endif
        </div>

    @endforeach
@endif
