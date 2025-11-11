@extends('layouts.vertical', ['title' => 'Users List'])

@section('content')

<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">All Users</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">22.63k</p>
                    <div>
                        <span class="badge text-success bg-success-subtle fs-12"><i class="bx bx-up-arrow-alt"></i>34.4%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:user-plus-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                        
                    </div>
                    <div>
                        <h4 class="mb-0">New Users</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">4.5k</p>
                    <div>
                        <span class="badge text-danger bg-danger-subtle fs-12"><i class="bx bx-down-arrow-alt"></i>8.1%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:user-check-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Active Users</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">1.03k</p>
                    <div>
                        <span class="badge text-success bg-success-subtle fs-12"><i class="bx bx-up-arrow-alt"></i>12.6%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:user-block-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Inactive Users</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">78</p>
                    <div>
                        <span class="badge text-success bg-success-subtle fs-12"><i class="bx bx-up-arrow-alt"></i>45.9%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">All Users List</h4>
                </div>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light rounded" data-bs-toggle="dropdown" aria-expanded="false">
                        This Month
                    </a>

                </div>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">

                        <thead class="bg-light-subtle">
    <tr>
        <th style="width:20px;">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="checkAll">
            <label class="form-check-label" for="checkAll"></label>
        </div>
        </th>
        <th>Name</th>
        <th>Status</th>
        <th>Contact</th>
        <th>City</th>
        <th>Location</th>
        <th>Action</th>
    </tr>
    </thead>

    <tbody>
    <!-- Row 1 -->
    <tr>
        <td>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="u1">
            <label class="form-check-label" for="u1">&nbsp;</label>
        </div>
        </td>

        <!-- Customer -->
    <td>
    <div class="d-flex align-items-center">
        <img src="/images/users/avatar-2.jpg" class="avatar-sm rounded-circle me-2" alt="">
        <div class="d-flex flex-column">
        <span class="fw-semibold">Michael A. Miner</span>
        
        </div>
    </div>
    </td>




    <td>
        <span class="badge bg-success-subtle text-success py-1 px-2">Active</span>
    </td>


    <td>
      <span class="fw-semibold">+966-554-8745</span>
    </td>


    <td>
      <span class="fw-semibold">Riyadh</span>
   
    </td>


    <td>
  
      <span class="fw-semibold">Saudi Arabia</span>


    </td>

    <!-- Action (unchanged) -->
    <td>
      <div class="d-flex gap-2">
        <a href="http://127.0.0.1:8000/users/details" class="btn btn-light btn-sm"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
        <a href="http://127.0.0.1:8000/users/details" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
        <a href="http://127.0.0.1:8000/users/details" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
      </div>
    </td>
  </tr>

  <!-- Copy the structure for other rows; just change values -->
</tbody>

                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            <div class="card-footer border-top">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

</div>

@endsection