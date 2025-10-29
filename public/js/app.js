window.createContact = function() {
    $('#modalTitle').text('Add Contact');
    $('#contact_id').val('');
    $.get('/contacts/create', function(data) {
        $('#form-body').html(data.html);
        $('#contactForm')[0].reset();  
        $('#contactModal').modal('show');
    });
};

window.editContact = function(id) {
    $.get(`/contacts/${id}/edit`, function(html) {
        $('#modalTitle').text('Edit Contact');
        $('#form-body').html(html);
        $('#contactModal').modal('show');
    });
};

window.deleteContact = function(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/contacts/${id}`,
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    if (res.success) {
                        loadContacts();
                        Swal.fire('Deleted!', res.message, 'success');
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message || 'Delete failed.', 'error');
                }
            });
        }
    });
};

$('#contactForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#contact_id').val();
    let url = id ? `/contacts/${id}` : '/contacts';
    let type = id ? 'PUT' : 'POST';

    let formData = new FormData(this);

    $.ajax({
        url: url,
        type: type,
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                $('#contactModal').modal('hide');
                loadContacts();
                Swal.fire('Success!', res.message, 'success');
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error!', xhr.responseJSON.message || 'Operation failed.', 'error');
        }
    });
});

let secondaryId = null;
window.initMerge = function(id) {
    secondaryId = id;
    $.get('/contacts/all', function(contacts) {
        let options = '<option value="">Select Master</option>';
        $.each(contacts, function(i, c) {
            if (c.id !== id) {
                options += `<option value="${c.id}">${c.name} (${c.email})</option>`;
            }
        });
        $('#master_contact').html(options);
        $('#mergeModal').modal('show');
    });
};

$('#master_contact').on('change', function() {
    let masterId = $(this).val();
    if (masterId && secondaryId) {
        $.post('/contacts/merge-preview', {
            master_id: masterId,
            secondary_id: secondaryId,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(res) {
            let html = '<table class="table"><thead><tr><th>Field</th><th>Master</th><th>Secondary</th><th>Action</th></tr></thead><tbody>';
            $.each(res.diff.standard, function(field, data) {
                html += `<tr><td>${field}</td><td>${data.master || '-'}</td><td>${data.secondary}</td><td>${data.action}</td></tr>`;
            });
            $.each(res.diff.custom, function(field, data) {
                html += `<tr><td>${field}</td><td>${data.master || '-'}</td><td>${data.secondary}</td><td>${data.action}</td></tr>`;
            });
            html += '</tbody></table>';
            $('#merge-preview').html(html);
        });
    }
});

$('#confirm-merge').on('click', function() {
    Swal.fire({
        title: 'Confirm Merge?',
        text: 'This will merge the contacts permanently. Review the preview above.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, merge!'
    }).then((result) => {
        if (result.isConfirmed) {
            let masterId = $('#master_contact').val();
            $.post('/contacts/merge', {
                master_id: masterId,
                secondary_id: secondaryId,
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(res) {
                if (res.success) {
                    $('#mergeModal').modal('hide');
                    loadContacts();
                    Swal.fire('Merged!', res.message, 'success');
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            });
        }
    });
});

function loadContacts() {
    let q = $('#search').val();
    let gender = $('#gender').val();
    $.get('/contacts/search', { q, gender }, function(html) {
        $('#contacts-table').html(html);
    });
}

$('#search, #gender').on('input change', function() {
    loadContacts();
});
