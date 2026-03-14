<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $title ?? '' }}</h4>

                    @if(isset($button))
                    <div>
                        {!! $button !!}
                    </div>
                    @endif

                    @if(isset($backButton) && $backButton == true)
                    <button type="button" class="btn btn-secondary btn-go-back">
                        <i class="fa fa-arrow-left"></i> Go Back
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>