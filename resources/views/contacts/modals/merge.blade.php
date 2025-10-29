<div class="modal fade" id="mergeModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Merge Contacts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Select <strong>Master Contact</strong> (this will be kept):</p>
                <select id="master_contact" class="form-control mb-3"></select>

                <hr>

                <div id="merge-preview" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirm-merge">Confirm Merge</button>
            </div>
        </div>
    </div>
</div>