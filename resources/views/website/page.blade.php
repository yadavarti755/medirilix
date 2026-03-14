@extends('layouts.website_layout')

@section('content')
<main>
    @include('components.website.page-header')

    <!-- Main Content -->
    <div class="container py-5" id="main-content" tabindex="-1">
        <div class="row g-4">
            <!-- Main Content -->
            <div class="{{ ($submenus && $submenus->count() > 0) ? 'col-lg-9 col-md-12 col-12' : 'col-12' }}">
                <!-- Overview Section -->
                <section id="overview" class="mb-5">
                    <div class="card shadow-sm border-0">
                        @if ($pageDetails->content)
                        <div class="card-body p-4 show_page_content">
                            <div class="editor-content-wrapper ck-content">
                                {!! obfuscateEmailsInHtml($pageDetails->content) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>
@endsection

@section("pages-scripts")
<script @cspNonce>
    $(document).ready(function() {
        // Run only after the CKEditor content is rendered
        if ($(".pages-datatable").length) {
            $(".pages-datatable").each(function() {
                // Avoid re-initializing if already done
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        searching: true,
                        ordering: true,
                        responsive: true
                    });
                }
            });
        }
    });
</script>
@endsection