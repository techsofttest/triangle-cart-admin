@extends('layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="font-heading mb-4 text-center">Terms & Conditions</h1>
            <div class="card border-0 shadow-sm p-4 p-lg-5" style="background-color: var(--c-white);">
                <div class="prose">
                    <p class="lead">Please read these terms and conditions carefully before using our service.</p>
                    
                    <h4 class="mt-5 mb-3">1. Use of Website</h4>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla porttitor accumsan tincidunt. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Donec rutrum congue leo eget malesuada.</p>
                    
                    <h4 class="mt-4 mb-3">2. Intellectual Property</h4>
                    <p>Cras ultricies ligula sed magna dictum porta. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a.</p>
                    
                    <h4 class="mt-4 mb-3">3. User Obligations</h4>
                    <p>Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Nulla quis lorem ut libero malesuada feugiat. Donec sollicitudin molestie malesuada. Pellentesque in ipsum id orci porta dapibus.</p>
                    
                    <h4 class="mt-4 mb-3">4. Limitation of Liability</h4>
                    <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Sed porttitor lectus nibh. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae.</p>
                    
                    <h4 class="mt-4 mb-3">5. Governing Law</h4>
                    <p>Quisque velit nisi, pretium ut lacinia in, elementum id enim. Cras ultricies ligula sed magna dictum porta. Vivamus suscipit tortor eget felis porttitor volutpat.</p>
                    
                    <p class="mt-5 text-muted small">Last updated: {{ date('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-heading { font-family: var(--f-head); }
    .prose p { margin-bottom: 1.5rem; line-height: 1.8; color: #555; font-family: var(--f-body); }
    .prose h4 { font-family: var(--f-head); color: var(--c-primary); }
</style>
@endsection
