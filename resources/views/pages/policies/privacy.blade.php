@extends('layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="font-heading mb-4 text-center">Privacy Policy</h1>
            <div class="card border-0 shadow-sm p-4 p-lg-5" style="background-color: var(--c-white);">
                <div class="prose">
                    <p class="lead">Your privacy is important to us. It is Jodha Furniture's policy to respect your privacy regarding any information we may collect from you across our website, <a href="{{ url('/') }}">{{ url('/') }}</a>, and other sites we own and operate.</p>
                    
                    <h4 class="mt-5 mb-3">1. Information we collect</h4>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Satius est enim ad omnia respondere, quae a te dicta sunt. Duo Reges: constructio interrete. Quid ergo attinet dicere: Nihil haberem, quod reprehenderem, si finitas cupiditates haberent? Quoniam, si dissimiles essent, anteerepuerent.</p>
                    
                    <h4 class="mt-4 mb-3">2. How we use your information</h4>
                    <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Cras ultricies ligula sed magna dictum porta. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus.</p>
                    
                    <h4 class="mt-4 mb-3">3. Data security</h4>
                    <p>Nulla porttitor accumsan tincidunt. Donec rutrum congue leo eget malesuada. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Vivamus suscipit tortor eget felis porttitor volutpat.</p>
                    
                    <h4 class="mt-4 mb-3">4. Cookies</h4>
                    <p>Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Nulla quis lorem ut libero malesuada feugiat. Donec sollicitudin molestie malesuada. Pellentesque in ipsum id orci porta dapibus.</p>
                    
                    <h4 class="mt-4 mb-3">5. Third-party services</h4>
                    <p>Quisque velit nisi, pretium ut lacinia in, elementum id enim. Sed porttitor lectus nibh. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula.</p>
                    
                    <h4 class="mt-4 mb-3">6. Changes to this policy</h4>
                    <p>Donec sollicitudin molestie malesuada. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula.</p>
                    
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
