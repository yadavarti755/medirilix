@extends('layouts.website_layout')

@section('content')

<main>
    <h1 class="sr-only">Home – {{ getLocalizedDataFromObj($siteSettings, 'site_name') }}</h1>
    <!-- Main Carousel -->
    <section class="carousel-section">
        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel" aria-label="Main image carousel">
            <!-- Carousel Indicators -->
            <div class="carousel-indicators">
                @foreach ($sliders as $key => $slider)
                <button
                    type="button"
                    data-bs-target="#mainCarousel"
                    data-bs-slide-to="{{ $key }}"
                    class="{{ $key === 0 ? 'active' : '' }} rounded-circle"
                    aria-current="{{ $key === 0 ? 'true' : 'false' }}"
                    aria-label="Slide {{ $key + 1 }}">
                </button>
                @endforeach
            </div>

            <!-- Carousel Items -->
            <div class="carousel-inner rounded-3 elevation-4">
                @foreach ($sliders as $key => $slider)
                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                    <img
                        src="{{ $slider->file_url }}"
                        class="d-block w-100"
                        alt=""
                        role="presentation" />
                </div>
                @endforeach
            </div>

            <!-- Prev / Next Arrows -->
            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev" aria-label="Previous slide">
                <span class="carousel-control-prev-icon bg-primary bg-opacity-25 rounded-circle p-3 elevation-2" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next" aria-label="Next slide">
                <span class="carousel-control-next-icon bg-primary bg-opacity-25 rounded-circle p-3 elevation-2" aria-hidden="true"></span>
            </button>
        </div>

        <!-- ✅ Centered Pause/Play Button -->
        <div class="d-flex justify-content-center mt-3 carousel-btn-wrapper">
            <button
                id="carouselPausePlayBtn"
                class="btn btn-outline-primary rounded-circle"
                type="button"
                aria-pressed="true"
                aria-label="Pause carousel">
                <i class="fas fa-pause" aria-hidden="true"></i>
                <span class="visually-hidden">Pause carousel</span>
            </button>
        </div>
    </section>

    <!-- Announcements Ticker -->
    <section
        class="announcements-ticker bg-light border-top border-bottom py-1 elevation-1"
        role="region" aria-label="Announcements" id="announcement-ticker">
        <div class="container">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white px-3 py-1 rounded-pill me-3 elevation-1 d-flex align-items-center gap-1">
                    <i class="fa fa-bell" aria-hidden="true"></i>
                    <span class="fw-medium">{{ __('title.announcements') }}</span>
                </div>
                <div class="ticker-wrapper flex-grow-1 overflow-hidden announcement-ticker" aria-label="Scrolling announcements">
                    <div class="ticker-content" role="list" aria-live="polite">
                        @if ($announcements->count() > 0)
                        @foreach ($announcements as $announcement)
                        <a role="listitem" href="{{ $announcement->file_or_link == 'file'? getLocalizedDataFromObj($announcement, 'file_url') : $announcement->page_link }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-dark me-4">
                            <i class="fa fa-circle text-danger" aria-hidden="true"></i> {{ getLocalizedDataFromObj($announcement, 'title') }}
                            @if($announcement->file_or_link == 'file')
                            @php
                            $url = getLocalizedDataFromObj($announcement, 'file_url');
                            $fileSize = getFileSize($url);
                            $fileType = getFileType($url);
                            $fileSoftwareRequired = getRequiredSoftware($fileType);
                            @endphp
                            <span class="d-block text-red ms-3">
                                {{ __('title.type') }}: {{ $fileType }} |
                                {{ __('title.size') }}: {{ $fileSize }} |
                                {{ $fileSoftwareRequired }}
                            </span>
                            @endif
                        </a>
                        @endforeach
                        @else
                        <span class="me-4">{{ __('title.no_announcements') }}</span>
                        @endif
                    </div>
                </div>

                <button id="announcementPauseBtn"
                    class="btn btn-sm btn-outline-primary rounded-circle ms-2 ticker-control elevation-1 ripple-effect btn-only-icon-square"
                    aria-label="Pause announcements" aria-controls="announcement-ticker" aria-pressed="true">
                    <i class="fas fa-pause"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- About Section with 3 columns -->
    <section class="py-5 bg-primary" id="main-content" tabindex="-1">
        <div class="container">
            <div class="row g-4">
                <!-- About Us Column -->
                <div class="col-lg-4">
                    <div
                        class="card border-0 elevation-4 rounded-3 h-100 hover-elevation-5 ripple-effect">
                        <div class="card-body p-4">
                            @if($homeAbout && $homeAbout->image)
                            <div
                                class="position-relative rounded-3 overflow-hidden elevation-2 mb-4"
                                style="height: 200px">
                                <img
                                    src="{{ $homeAbout->image_path }}"
                                    alt="Exterior view of {{ getLocalizedDataFromObj($homeAbout, 'title') }} building"
                                    class="img-fluid w-100 h-100 object-fit-cover hover-scale" />
                            </div>
                            @endif
                            <h2 class="text-primary mb-3">
                                {{ getLocalizedDataFromObj($homeAbout, 'title') }}
                            </h2>
                            <p class="text-muted mb-4">
                                {{ getLocalizedDataFromObj($homeAbout, 'description') }}
                            </p>
                            @if($homeAbout)
                            <a
                                href="{{ $homeAbout->button_link }}"
                                class="btn btn-primary rounded-pill elevation-2 ripple-effect">
                                <i class="fas fa-info-circle me-2"></i> {{ __('title.learn_more') }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Social Media Column -->
                <div class="col-lg-4">
                    <div
                        class="card border-0 elevation-4 rounded-3 h-100 hover-elevation-5 ripple-effect">
                        <div class="card-body p-4">
                            <div>
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    @foreach($socialMedias as $key => $socialMedia)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $key == 0 ? 'active': '' }}" id="pills-{{ $key+1 }}-tab" data-bs-toggle="pill" data-bs-target="#pills-{{ $key+1 }}" type="button" role="tab" aria-controls="pills-{{ $key+1 }}" aria-selected="true">
                                            <i class="{{ $socialMedia->icon_class }}"></i>
                                            <span class="btn text-white bg-primary sr-only">{{ $socialMedia->name }}</span>
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content social-media-tab-content" id="pills-tabContent">
                                    @foreach($socialMedias as $key => $socialMedia)
                                    <div class="tab-pane fade {{ $key == 0 ? 'show active': '' }}" id="pills-{{ $key+1 }}" role="tabpanel" aria-labelledby="pills-{{ $key+1 }}-tab" tabindex="0">
                                        <div class="px-2 border rounded-3 elevation-2 hover-elevation-3 ripple-effect">
                                            {!! $socialMedia->embed_code !!}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hon'ble Leaders Column -->
                <div class="col-lg-4">
                    <div
                        class="card border-0 elevation-4 rounded-3 h-100 hover-elevation-5 ripple-effect">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column gap-4">
                                @foreach ($whoIsWhos as $whoIsWho)
                                <div class="text-center">
                                    <div
                                        class="position-relative mx-auto mb-3 rounded-3 overflow-hidden">
                                        <img
                                            src="{{ $whoIsWho->image_full_path }}"
                                            alt="{{ getLocalizedDataFromObj($whoIsWho, 'name') }}"
                                            class="img-fluid object-fit-cover hover-scale elevation-2 rounded-3"
                                            style="height: 150px; object-fit: contain" />
                                    </div>
                                    <h3 class="h5 text-primary">{{ getLocalizedDataFromObj($whoIsWho, 'name') }}</h3>
                                    <p class="text-muted mb-0">
                                        {{ getLocalizedDataFromObj($whoIsWho, 'designation') }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Media section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card shadow border-0 gallery_card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title mb-0 text-white">
                                {{ __('title.photo_gallery') }}
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @if ($photoGalleries->count() > 0)
                            <div class="photo_gallery_slider">
                                @foreach ($photoGalleries as $photoGallery)
                                <div>
                                    <a href="{{ route('photo-gallery.view-details', base64_encode($photoGallery->id)) }}"
                                        class="gallery-item rounded-0">
                                        <div
                                            class="position-relative overflow-hidden elevation-2 hover-elevation-3">
                                            <img
                                                src="{{ $photoGallery->featured_image_full_path }}"
                                                alt="{{ getLocalizedDataFromObj($photoGallery, 'title') }}"
                                                class="w-100 gallery-img rounded-0" />
                                            <div
                                                class="gallery-overlay d-flex align-items-end">
                                                <div class="p-2 text-white">
                                                    <p class="small mb-0">
                                                        {{ getLocalizedDataFromObj($photoGallery, 'title') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="gallery-action">
                                                <button type="button"
                                                    class="btn btn-sm btn-primary rounded-circle elevation-2 ripple-effect">
                                                    <i class="fas fa-paper-plane"></i> <span class="sr-only">
                                                        View Photo Gallery Details</span>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-center align-items-center gallery-button-wrapper py-2">
                                <h4 id="ministry-heading" class="visually-hidden">Photo Gallery</h4>
                                <div class="slider-controls" role="group" aria-label="Slider Controls">
                                    <!-- Prev Button -->
                                    <button id="photoGalleryPrevBtn" class="btn btn-sm btn-outline-primary rounded-circle" aria-label="Previous slide">
                                        <i class="fa fa-chevron-left"></i> <span class="sr-only">Previous</span>
                                    </button>
                                    <!-- Pause Button -->
                                    <button id="photoGalleryPauseBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Pause carousel">
                                        <i class="fa fa-pause"></i> <span class="sr-only">Pause</span>
                                    </button>
                                    <!-- Play Button -->
                                    <button id="photoGalleryPlayBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Play carousel" style="display:none;">
                                        <i class="fa fa-play"></i> <span class="sr-only">Play</span>
                                    </button>
                                    <!-- Next -->
                                    <button id="photoGalleryNextBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Next slide">
                                        <i class="fa fa-chevron-right"></i> <span class="sr-only">Next</span>
                                    </button>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-danger">
                                <strong>{{ __('title.no_photos_found') }}</strong>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-end mb-0 rounded-0">
                            <a
                                href="{{ route('photo-gallery') }}"
                                class="btn btn-primary rounded-pill elevation-2 ripple-effect">
                                <i class="fas fa-images me-2"></i> {{ __('title.browse_all_photos') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card whats_new_card shadow border-0">
                        <div class="card-header bg-primary">
                            <h3 class="card-title mb-0 text-white">
                                {{ __('title.whats_new') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            @if (count($whatsNew) > 0)
                            @foreach ($whatsNew as $update)
                            @php
                            $url = '#';
                            if($update->type == 'tender'):
                            $url = getLocalizedDataFromObj($update, 'file_url');
                            endif;

                            if($update->type == 'event'):
                            $url = route('event.view-details', $update->id);
                            endif;

                            if($update->type == 'circular'):
                            $url = getLocalizedDataFromObj($update, 'file_url');
                            $fileSize = getFileSize($url);
                            $fileType = getFileType($url);
                            $fileSoftwareRequired = getRequiredSoftware($fileType);
                            endif;
                            @endphp
                            <a href="{{ $url }}" class="text-decoration-none d-block">
                                <div>
                                    <span class="badge bg-danger">
                                        @if($update->type == 'tender')
                                        <i class="fas fa-bullhorn me-1"></i> {{ __('title.tender') }}
                                        @endif

                                        @if($update->type == 'event')
                                        <i class="fas fa-calendar-alt me-1"></i> {{ __('title.event') }}
                                        @endif

                                        @if($update->type == 'circular')
                                        <i class="fas fa-file-alt me-1"></i> {{ getLocalizedDataFromObj($update->circularCategory, 'name') }}
                                        @endif

                                        @if($update->type == 'estt_document')
                                        <i class="fas fa-file me-1"></i> {{ __('title.estt_document') }}
                                        @endif
                                    </span>
                                    <div>
                                        <h4 class="h6 text-primary mb-0 mt-1">
                                            {{ getLocalizedDataFromObj($update, 'title') }}
                                        </h4>
                                    </div>
                                    <div>
                                        <span class="text-dark">
                                            @if($update->type == 'tender')
                                            {{ date('d/m/Y', strtotime($update->publish_date)) }}
                                            @endif

                                            @if($update->type == 'event')
                                            {{ date('d/m/Y', strtotime($update->date)) }}
                                            @endif

                                            @if($update->type == 'circular')
                                            {{ date('d/m/Y', strtotime($update->published_date)) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </a>
                            <span class="d-block mb-2 text-red">
                                {{ __('title.type') }}: {{ $fileType }} |
                                {{ __('title.size') }}: {{ $fileSize }} |
                                {{ $fileSoftwareRequired }}
                            </span>
                            @endforeach
                            @else
                            <div class="text-center">
                                {{ __('title.no_data_available') }}
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-end">
                            <a
                                href="{{ url()->to('/whats-new/update') }}"
                                class="btn btn-primary rounded-pill elevation-2 ripple-effect">
                                <i class="fas fa-list me-2"></i> {{ __('title.view_all_updates') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card shadow border-0 gallery_card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title mb-0 text-white">
                                {{ __('title.video_gallery') }}
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @if ($videoGalleries->count() > 0)
                            <div class="video_gallery_slider">
                                @foreach ($videoGalleries as $videoGallery)
                                <div>
                                    <a
                                        href="{{ route('video-gallery.view-details', base64_encode($videoGallery->id)) }}"
                                        class="video-item rounded-0">
                                        <div
                                            class="position-relative overflow-hidden elevation-2 hover-elevation-3">
                                            <img
                                                src="{{ $videoGallery->thumbnail_image_full_path }}"
                                                alt="{{ getLocalizedDataFromObj($videoGallery, 'title') }}"
                                                class="w-100 rounded-0" />
                                            <div
                                                class="video-play-button elevation-3 ripple-effect">
                                                <i class="fas fa-play"></i>
                                            </div>
                                            <div class="video-duration">5:30</div>
                                            <div class="video-overlay d-flex align-items-end">
                                                <div class="p-2 text-white">
                                                    <p class="small mb-0">
                                                        {{ getLocalizedDataFromObj($videoGallery, 'title') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-center align-items-center py-2">
                                <h4 id="ministry-heading" class="visually-hidden">Video Gallery</h4>
                                <div class="slider-controls" role="group" aria-label="Slider Controls">
                                    <button id="videoGalleryPrevBtn" class="btn btn-sm btn-outline-primary rounded-circle" aria-label="Previous slide">
                                        <i class="fa fa-chevron-left"></i> <span class="sr-only">Prev</span>
                                    </button>
                                    <button id="videoGalleryPauseBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Pause carousel">
                                        <i class="fa fa-pause"></i> <span class="sr-only">Pause</span>
                                    </button>
                                    <button id="videoGalleryPlayBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Play carousel" style="display:none;">
                                        <i class="fa fa-play"></i> <span class="sr-only">Play</span>
                                    </button>
                                    <button id="videoGalleryNextBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Next slide">
                                        <i class="fa fa-chevron-right"></i> <span class="sr-only">Next</span>
                                    </button>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-danger mb-0 rounded-0">
                                <strong>{{ __('title.no_videos_found') }}</strong>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-end">
                            <a
                                href="{{ route('video-gallery') }}"
                                class="btn btn-primary rounded-pill elevation-2 ripple-effect">
                                <i class="fas fa-video me-2"></i> {{ __('title.browse_all_videos') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Quick Links -->
    <section class="px-md-5 px-2 py-md-5 py-4 bg-gray">
        <div class="container">
            <div class="row">
                <!-- Quick Links -->
                <div class="col-12">
                    <div
                        class="card shadow border-0 rounded-3 ">
                        <div class="card-header bg-primary text-white rounded-top-3">
                            <h4 class="card-title mb-0">{{ __('title.quick_links') }}</h4>
                        </div>
                        <div class="card-body p-4">
                            @if ($quickLinks->count() > 0)
                            <ul class="quick_links_list d-flex">
                                @foreach ($quickLinks as $quickLink)
                                <li>
                                    <a href="{{ $quickLink->link }}" target="_blank" rel="noopener noreferrer" aria-label="{{ getLocalizedDataFromObj($quickLink, 'title') }} (opens in a new tab)" class="text-decoration-none fw-medium text-primary ripple-effect external-link">
                                        {{ getLocalizedDataFromObj($quickLink, 'title') }}
                                    </a>
                                    @if(getLocalizedDataFromObj($quickLink, 'description'))
                                    <small class="text-muted d-block">{{ getLocalizedDataFromObj($quickLink, 'description') }}</small>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <div class="alert alert-danger">
                                <strong>{{ __('title.no_quick_links') }}</strong>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-end">
                            <a
                                href="{{ url()->to('quick-links') }}"
                                class="btn btn-primary rounded-pill elevation-2 ripple-effect">
                                <i class="fas fa-link me-2"></i> {{ __('title.view_all_quick_links') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Government Portals Slider -->
    <section class="pt-5 pb-4 bg-white">
        <div class="container">
            <div class="position-relative">
                <div class="portal-slider">
                    @if ($governmentPortals->count() > 0)
                    <div class="slider govt_ministry">
                        @foreach ($governmentPortals as $governmentPortal)
                        <div class="portal-slide px-2">
                            <a
                                href="{{ $governmentPortal->link }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ getLocalizedDataFromObj($governmentPortal, 'title') }}"
                                class="portal-link text-decoration-none">
                                <div
                                    class="card h-100 elevation-2 hover-elevation-3 rounded-3 ripple-effect">
                                    <div class="card-body text-center p-3">
                                        <img
                                            src="{{ $governmentPortal->file_name_full_path }}"
                                            alt=""
                                            aria-hidden="true"
                                            class="img-fluid mb-2"
                                            style="height: 100px; object-fit: contain" />
                                        <div
                                            class="small fw-medium d-flex align-items-center justify-content-center text-primary">
                                            {{ getLocalizedDataFromObj($governmentPortal, 'title') }}
                                            <i class="fas fa-external-link-alt ms-1 small"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center align-items-center mb-3 mt-3">
                        <h4 id="ministry-heading" class=" btn text-white bg-primary visually-hidden">Government Ministry Partners</h4>
                        <div class="slider-controls" role="group" aria-label="Slider Controls">
                            <!-- Prev Button -->
                            <button id="govtPrevBtn" class="btn btn-sm btn-outline-primary rounded-circle" aria-label="Previous slide">
                                <i class="fa fa-chevron-left"></i> <span class="sr-only">Previous</span>
                            </button>
                            <!-- Pause Button -->
                            <button id="pauseBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Pause carousel">
                                <i class="fa fa-pause"></i> <span class="sr-only">Pause</span>
                            </button>
                            <!-- Play Button -->
                            <button id="playBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Play carousel" style="display:none;">
                                <i class="fa fa-play"></i> <span class="sr-only">Play</span>
                            </button>
                            <!-- Next Button -->
                            <button id="govtNextBtn" class="btn btn-sm btn-outline-primary rounded-circle ms-2" aria-label="Next slide">
                                <i class="fa fa-chevron-right"></i> <span class="sr-only">Next</span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>
@endsection