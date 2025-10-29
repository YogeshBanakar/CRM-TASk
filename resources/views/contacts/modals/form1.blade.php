<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="contactForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="contact_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Gender *</label>
                            <div>
                                <input type="radio" name="gender" value="male" id="male"> <label for="male">Male</label>
                                <input type="radio" name="gender" value="female" id="female"> <label for="female">Female</label>
                                <input type="radio" name="gender" value="other" id="other"> <label for="other">Other</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Profile Image</label>
                            <input type="file" name="profile_image" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Additional File</label>
                            <input type="file" name="additional_file" class="form-control">
                        </div>
                    </div>

                    <hr>
                    <h5>Custom Fields</h5>
                    <div id="custom-fields">
                        @foreach(\App\Models\CustomField::all() as $field)
                        <div class="mb-3">
                            <label>{{ $field->name }}</label>
                            @if($field->type === 'text')
                                <input type="text" name="custom_fields[{{ $field->id }}]" class="form-control">
                            @elseif($field->type === 'date')
                                <input type="date" name="custom_fields[{{ $field->id }}]" class="form-control">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>