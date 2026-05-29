@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: var(--c-linen); min-height: 80vh;">
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--c-white);">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; border: 1px solid var(--c-gold);">
                            <i class="fa-solid fa-user fa-2x text-gold"></i>
                        </div>
                        <h5 class="font-heading mb-1">{{ explode(' ', $customer->name)[0] }}</h5>
                        <p class="text-muted small mb-0">{{ $customer->email }}</p>
                    </div>
                    
                    <hr class="my-4 opacity-10">
                    
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <a href="{{ route('profile.dashboard') }}" class="d-flex align-items-center gap-3 text-dark text-decoration-none">
                                <i class="fa-solid fa-gauge-high text-gold" style="width: 20px;"></i>
                                <span class="fw-bold">Dashboard</span>
                            </a>
                        </li>
                        <li class="mb-3">
                            <a href="{{ route('profile.orders') }}" class="d-flex align-items-center gap-3 text-muted text-decoration-none hover-gold transition-all">
                                <i class="fa-solid fa-box" style="width: 20px;"></i>
                                <span>My Orders</span>
                            </a>
                        </li>
                        <li class="mb-3">
                            <a href="{{ route('profile.addresses') }}" class="d-flex align-items-center gap-3 text-muted text-decoration-none hover-gold transition-all">
                                <i class="fa-solid fa-location-dot" style="width: 20px;"></i>
                                <span>My Addresses</span>
                            </a>
                        </li>
                        <li class="mt-5">
                            <form action="/customer/logout" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item p-0 d-flex align-items-center gap-3 text-danger border-0 bg-transparent">
                                    <i class="fa-solid fa-right-from-bracket" style="width: 20px;"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="font-heading mb-2">My Addresses</h2>
                        <p class="text-muted">Manage your shipping and billing addresses.</p>
                    </div>
                    <button class="btn btn-primary-custom2 rounded-0" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="resetForm()">
                        <i class="fa-solid fa-plus me-2"></i> Add New Address
                    </button>
                </div>

                <div class="row">
                    @forelse($addresses as $address)
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--c-white);">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="font-heading mb-0">{{ $address->name }} @if($address->is_default) <span class="badge bg-gold ms-2" style="font-size: 10px;">DEFAULT</span> @endif</h6>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0 shadow-none" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="editAddress({{ json_encode($address) }})"><i class="fa-solid fa-pen-to-square me-2 small"></i> Edit</a></li>
                                            <li><a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="deleteAddress({{ $address->id }})"><i class="fa-solid fa-trash me-2 small"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="text-muted small mb-1">{{ $address->phone }}</p>
                                <p class="text-dark small mb-3">
                                    {{ $address->address_line1 }}{{ $address->address_line2 ? ', ' . $address->address_line2 : '' }}<br>
                                    {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}<br>
                                    {{ $address->country }}
                                </p>
                                <div class="mt-auto">
                                    @if(!$address->is_default)
                                        <button class="btn btn-sm btn-link p-0 text-gold text-decoration-none small fw-bold hover-underline" onclick="setDefault({{ $address->id }})">Set as Default</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm p-5 text-center" style="background-color: var(--c-white);">
                                <i class="fa-solid fa-location-dot fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted font-heading">You haven't saved any addresses yet.</h5>
                                <div class="mt-3">
                                    <button class="btn btn-primary-custom2 rounded-0" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="resetForm()">Add Your First Address</button>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-light p-4">
                <h5 class="modal-title font-heading" id="modalTitle">Add New Address</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addressForm">
                @csrf
                <div id="methodField"></div>
                <input type="hidden" id="address_id" name="id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="small text-muted mb-2 uppercase-tracking">Address Name (e.g. Home, Office)</label>
                            <input type="text" name="name" id="name" class="form-control luxury-input-minimal" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted mb-2 uppercase-tracking">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control luxury-input-minimal" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted mb-2 uppercase-tracking">Pin Code</label>
                            <input type="text" name="postal_code" id="postal_code" class="form-control luxury-input-minimal" required>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted mb-2 uppercase-tracking">Address Line 1</label>
                            <input type="text" name="address_line1" id="address_line1" class="form-control luxury-input-minimal" required>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted mb-2 uppercase-tracking">Address Line 2 (Optional)</label>
                            <input type="text" name="address_line2" id="address_line2" class="form-control luxury-input-minimal">
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted mb-2 uppercase-tracking">City</label>
                            <input type="text" name="city" id="city" class="form-control luxury-input-minimal" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted mb-2 uppercase-tracking">State</label>
                            <input type="text" name="state" id="state" class="form-control luxury-input-minimal" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted mb-2 uppercase-tracking">Country</label>
                            <input type="text" name="country" id="country" class="form-control luxury-input-minimal" value="India" required>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1">
                                <label class="form-check-label small" for="is_default">
                                    Set as default address
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-light p-4 text-end">
                    <button type="button" class="btn btn-link text-muted text-decoration-none small" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-luxury-solid px-4 py-2" id="saveBtn">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .luxury-input-minimal {
        border-radius: 0;
        border: none;
        border-bottom: 1px solid #e0e0e0;
        padding-left: 0;
        padding-right: 0;
        transition: all 0.3s ease;
        background: transparent !important;
    }
    .luxury-input-minimal:focus {
        box-shadow: none;
        border-bottom-color: var(--c-gold);
    }
    .btn-luxury-solid {
        background-color: var(--c-gold);
        color: #fff;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s ease;
    }
    .btn-luxury-solid:hover {
        background-color: var(--c-primary);
        color: #fff;
    }
    .border-top-light { border-top: 1px solid rgba(0,0,0,0.05) !important; }
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; font-size: 10px; }
</style>

<style>
    .hover-gold:hover {
        color: var(--c-gold) !important;
        transform: translateX(5px);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .text-gold {
        color: var(--c-gold) !important;
    }
    .font-heading {
        font-family: var(--f-head);
    }
    .bg-gold {
        background-color: var(--c-gold);
    }
</style>

<script>
function resetForm() {
    document.getElementById('addressForm').reset();
    document.getElementById('address_id').value = '';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('modalTitle').innerText = 'Add New Address';
    document.getElementById('saveBtn').innerText = 'Save Address';
}

function editAddress(address) {
    resetForm();
    document.getElementById('modalTitle').innerText = 'Edit Address';
    document.getElementById('saveBtn').innerText = 'Update Address';
    document.getElementById('address_id').value = address.id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    document.getElementById('name').value = address.name;
    document.getElementById('phone').value = address.phone;
    document.getElementById('postal_code').value = address.postal_code;
    document.getElementById('address_line1').value = address.address_line1;
    document.getElementById('address_line2').value = address.address_line2 || '';
    document.getElementById('city').value = address.city;
    document.getElementById('state').value = address.state;
    document.getElementById('country').value = address.country;
    document.getElementById('is_default').checked = address.is_default == 1;
    
    new bootstrap.Modal(document.getElementById('addressModal')).show();
}

document.getElementById('addressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('address_id').value;
    const url = id ? `/addresses/${id}` : '/addresses';
    const btn = document.getElementById('saveBtn');
    const originalText = btn.innerText;
    
    btn.disabled = true;
    btn.innerText = 'Saving...';
    
    fetch(url, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alertify.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            alertify.error(data.message || 'Something went wrong');
        }
        btn.disabled = false;
        btn.innerText = originalText;
    })
    .catch(error => {
        console.error('Error:', error);
        alertify.error('An error occurred');
        btn.disabled = false;
        btn.innerText = originalText;
    });
});

function deleteAddress(id) {
    alertify.confirm('Delete Address', 'Are you sure you want to delete this address?', function() {
        fetch(`/addresses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alertify.success(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                alertify.error(data.message);
            }
        });
    }, function() {});
}

function setDefault(id) {
    fetch(`/addresses/${id}/default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alertify.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            alertify.error(data.message);
        }
    });
}
</script>
@endsection
