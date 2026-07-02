<div class="modal modal-blur fade" id="contact-us-modal" tabindex="-1" role="dialog" aria-labelledby="contact-us-modal-label" aria-hidden="true" aria-label="Contact Us Details Modal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="contact-us-modal-label">{{__('page.CONTACT_US_DETAILS')}}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <!-- First Name -->
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <label class="form-label fw-bold me-3" style="min-width: 100px;">{{__('page.NAME')}}:</label>
                            <div class="form-control-plaintext" id="contact-name">Not found</div>
                        </div>
                    </div>
                    <!-- Email -->
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <label class="form-label fw-bold me-3" style="min-width: 100px;">{{__('page.EMAIL')}}:</label>
                            <div class="form-control-plaintext" id="contact-email">not found</div>
                        </div>
                    </div>
                    <!-- Mobile -->
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <label class="form-label fw-bold me-3" style="min-width: 100px;">{{__('page.MOBILE')}}:</label>
                            <div class="form-control-plaintext" id="contact-mobile">not found</div>
                        </div>
                    </div>
                    <!-- Message -->
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <label class="form-label fw-bold me-3" style="min-width: 100px;">{{__('page.MESSAGE')}}:</label>
                            <div class="form-control-plaintext" id="contact-message">not found</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>