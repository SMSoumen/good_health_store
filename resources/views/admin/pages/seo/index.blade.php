@extends('admin.layout')

@section('title')
    SEO Management
@endsection

@section('content')
    <x-admin.breadcrumb title="SEO Management" subtitle="Manage SEO settings for your e-shop" :breadcrumbs="[['label' => 'SEO Management']]" />

    <div class="container-fluid py-4">
        <!-- Web-only SEO Notice -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-0" role="alert">
                    <i class="fas fa-info-circle me-3 fa-lg text-primary"></i>
                    <div>
                        <h6 class="alert-heading mb-1 font-weight-bold">Information</h6>
                        <p class="mb-0">Please note that these SEO settings are specifically designed for and applied to
                            the <strong>Web Frontend</strong> of your store. They do not affect mobile application indexing
                            or deep linking directly.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Overview Cards -->
        <div class="row mb-4">
            <!-- Global SEO Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="text-primary text-uppercase mb-1" style="font-size: 0.75rem; font-weight: 700;">
                                    Global SEO
                                </h6>
                                <h4 class="mb-0 font-weight-bold">
                                    {{ $globalSeo ? 'Configured' : 'Not Set' }}
                                </h4>
                            </div>
                            <div class="icon-shape bg-gradient-primary text-white rounded-circle p-3">
                                <i class="fas fa-globe fa-lg"></i>
                            </div>
                        </div>
                        <a href="{{ route('admin.seo.global') }}" class="btn btn-primary btn-sm w-100 text-white">
                            <i class="fas fa-cog me-1"></i> Configure Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product SEO Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="text-success text-uppercase mb-1" style="font-size: 0.75rem; font-weight: 700;">
                                    Product SEO
                                </h6>
                                <h4 class="mb-0 font-weight-bold">
                                    {{ $productsWithSeo }}/{{ $totalProducts }}
                                </h4>
                                <small class="text-muted">configured</small>
                            </div>
                            <div class="icon-shape bg-gradient-success text-white rounded-circle p-3">
                                <i class="fas fa-box fa-lg"></i>
                            </div>
                        </div>
                        <a href="{{ route('admin.seo.products') }}" class="btn btn-success btn-sm w-100 text-white">
                            <i class="fas fa-edit me-1"></i> Manage Products
                        </a>
                    </div>
                </div>
            </div>

            <!-- Category SEO Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="text-info text-uppercase mb-1" style="font-size: 0.75rem; font-weight: 700;">
                                    Category SEO
                                </h6>
                                <h4 class="mb-0 font-weight-bold">
                                    {{ $categoriesWithSeo }}/{{ $totalCategories }}
                                </h4>
                                <small class="text-muted">configured</small>
                            </div>
                            <div class="icon-shape bg-gradient-info text-white rounded-circle p-3">
                                <i class="fas fa-tags fa-lg"></i>
                            </div>
                        </div>
                        <a href="{{ route('admin.seo.categories') }}" class="btn btn-info btn-sm w-100 text-white">
                            <i class="fas fa-edit me-1"></i> Manage Categories
                        </a>
                    </div>
                </div>
            </div>

            <!-- Blog SEO Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="text-warning text-uppercase mb-1" style="font-size: 0.75rem; font-weight: 700;">
                                    Blog SEO
                                </h6>
                                <h4 class="mb-0 font-weight-bold">
                                    {{ $blogsWithSeo }}/{{ $totalBlogs }}
                                </h4>
                                <small class="text-muted">configured</small>
                            </div>
                            <div class="icon-shape bg-gradient-warning text-white rounded-circle p-3">
                                <i class="fas fa-blog fa-lg"></i>
                            </div>
                        </div>
                        <a href="{{ route('admin.seo.blogs') }}" class="btn btn-warning btn-sm w-100 text-white">
                            <i class="fas fa-edit me-1"></i> Manage Blogs
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sitemap & Robots.txt Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-sitemap text-primary me-2"></i> Sitemap & Robots.txt
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="font-weight-bold mb-2">
                                        <i class="fas fa-file-code text-primary me-1"></i> Sitemap URL
                                    </h6>
                                    <p class="mb-2">
                                        <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-decoration-none">
                                            {{ url('/sitemap.xml') }}
                                        </a>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Submit this to Google Search Console
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="font-weight-bold mb-2">
                                        <i class="fas fa-robot text-primary me-1"></i> Robots.txt URL
                                    </h6>
                                    <p class="mb-2">
                                        <a href="{{ url('/robots.txt') }}" target="_blank" class="text-decoration-none">
                                            {{ url('/robots.txt') }}
                                        </a>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Controls search engine crawling
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('admin.seo.sitemap') }}" class="btn btn-primary text-white">
                                <i class="fas fa-cog me-1"></i> Manage Sitemap & Robots.txt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Best Practices -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-info text-white py-3">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-lightbulb me-2"></i> SEO Best Practices
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Keep meta titles under 60 characters
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Keep meta descriptions between 150-160 characters
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Use unique titles and descriptions for each page
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Include relevant keywords naturally
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Optimize images with descriptive alt text
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Submit sitemap to Google Search Console
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Use Open Graph tags for social sharing
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Implement structured data (JSON-LD)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-gradient-primary {
            background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(87deg, #11cdef 0, #1171ef 100%) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(87deg, #fb6340 0, #fbb140 100%) !important;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection
