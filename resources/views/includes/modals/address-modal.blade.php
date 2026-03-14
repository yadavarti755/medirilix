{{-- Address Modal --}}
<div class="modal modal-signin fade py-5" tabindex="-1" role="dialog" id="address-modal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded-5 shadow">
            <div class="modal-header p-sm-5 p-4 pb-sm-4 pb-4 border-bottom-0">
                <h4 class="fw-bold mb-0">Enter New Address</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-sm-5 p-4 pt-sm-0 pt-0">
                <form class="" id="address_form" method="post">
                    @csrf
                    <input type="hidden" name="hidden_operation_type" id="hidden_operation_type">
                    <div class="row">
                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-4" id="name" name="name"
                                    placeholder="Name">
                                <label for="name">Name <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control rounded-4" id="phone_number"
                                    name="phone_number" placeholder="Phone Number" minlength="10" maxlength="10">
                                <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control rounded-4" id="alt_phone_number"
                                    name="alt_phone_number" placeholder="Alternate Phone Number" minlength="10"
                                    maxlength="10">
                                <label for="alt_phone_number">Alternate Phone (Optional)</label>
                            </div>
                        </div>

                        <div class="col-lg-12 col-sm-12 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <textarea class="form-control rounded-4" id="address" name="address"
                                    placeholder="Address 1" rows="4" style="height: unset;"></textarea>
                                <label for="address">Address (Area & Street) <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-4" id="locality" name="locality"
                                    placeholder="Locality">
                                <label for="locality">Locality <span class="text-danger">*</span></label>
                            </div>
                        </div>



                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-4" id="landmark" name="landmark"
                                    placeholder="Landmark">
                                <label for="landmark">Landmark (Optional)</label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-4" id="city" name="city"
                                    placeholder="City">
                                <label for="landmark">City <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <select name="country" id="country" class="form-control rounded-4">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                                <label for="country">Country <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <select name="state" id="state" class="form-control rounded-4">
                                    <option value="">Select State</option>
                                </select>
                                <label for="state">State <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12 mb-3">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control rounded-4" id="pin_code" name="pin_code"
                                    placeholder="Pin Code">
                                <label for="pin_code">Pin Code <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-purple" id="sa-submit-btn"><i class="fas fa-save"></i> Save Address </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>