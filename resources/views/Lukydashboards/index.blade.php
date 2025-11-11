@extends('layouts.vertical', ['title' => 'Dashboard'])
@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection
@section('content')
    <div class="row">
        <div class="col-xxl-5">
            <div class="row">


                <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                        <div class="avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:bag-4-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                        </div>
                        </div>
                        <div class="col-6 text-end">
                        <p class="text-muted mb-0 text-truncate">Gross Requests</p>
                        <h3 class="text-dark mt-1 mb-0">SAR 1.23M</h3>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        <span class="text-success"><i class="bx bxs-up-arrow fs-12"></i> 3.1%</span>
                        <span class="text-muted ms-1 fs-12">WoW</span>
                        </div>
                        <a href="#!" class="text-reset fw-semibold fs-12">View More</a>
                    </div>
                    </div>
                </div>
                </div>

                <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                        <div class="avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                        </div>
                        </div>
                        <div class="col-6 text-end">
                        <p class="text-muted mb-0 text-truncate">Net Revenue</p>
                        <h3 class="text-dark mt-1 mb-0">SAR 872k</h3>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        <span class="text-success"><i class="bx bxs-up-arrow fs-12"></i> 2.4%</span>
                        <span class="text-muted ms-1 fs-12">MoM</span>
                        </div>
                        <a href="#!" class="text-reset fw-semibold fs-12">View More</a>
                    </div>
                    </div>
                </div>
                </div>

                <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                        <div class="avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                        </div>
                        </div>
                        <div class="col-6 text-end">
                        <p class="text-muted mb-0 text-truncate">Completed Requests</p>
                        <h3 class="text-dark mt-1 mb-0">9,760</h3>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        <span class="text-success"><i class="bx bxs-up-arrow fs-12"></i> 1.8%</span>
                        <span class="text-muted ms-1 fs-12">WoW</span>
                        </div>
                        <a href="#!" class="text-reset fw-semibold fs-12">View More</a>
                    </div>
                    </div>
                </div>
                </div>

                <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                        <div class="avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:forbidden-circle-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                        </div>
                        </div>
                        <div class="col-6 text-end">
                        <p class="text-muted mb-0 text-truncate">Cancellation Rate</p>
                        <h3 class="text-dark mt-1 mb-0">3.2%</h3>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        <span class="text-success"><i class="bx bxs-down-arrow fs-12"></i> 0.4%</span>
                        <span class="text-muted ms-1 fs-12">MoM</span>
                        </div>
                        <a href="#!" class="text-reset fw-semibold fs-12">View More</a>
                    </div>
                    </div>
                </div>
                </div>

                    <div class="col-12">
                        <!-- one row start -->
                        <div class="card bg-light-subtle">
                            <div class="card-header">
                            <h4 class="card-title">Monthly Revenue Champions</h4>
                        </div>
                            <div class="card-body">
                                <div class="row">
        
                                
                                    <div class="col-lg-6">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar bg-light d-flex align-items-center justify-content-center rounded">
                                                <iconify-icon icon="solar:cup-star-bold" class="fs-35 text-primary"></iconify-icon>
                                            </div>

                                            <div>
                                                <p class="text-dark fw-medium fs-18 mb-1">Tbuk Luxe Lounge</p>
                                                <p class="mb-0">7854K</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar bg-light d-flex align-items-center justify-content-center rounded">
                                                <iconify-icon icon="solar:cup-star-bold" class="fs-35 text-primary"></iconify-icon>
                                            </div>

                                            <div>
                                                <p class="text-dark fw-medium fs-18 mb-1">Expert Customer Service</p>
                                                <p class="mb-0">7000K</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- oen row end -->
                    </div>
            </div> <!-- end row -->
        </div> <!-- end col -->

        <div class="col-xxl-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Provider Revenue Analytics</h4>
                        
                        <div>
                            <input type="text" id="humanfd-datepicker" class="form-control" placeholder="Select Date">
                        </div>
                    </div> <!-- end card-title-->

                    <div dir="ltr" class="mt-4">
                        <div id="provider-performance-chart" class="apex-charts"></div>
                    </div>
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> 
    </div> <!-- end row -->

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Returning Conversion Rate</h5>
                    <div id="conversions" class="apex-charts mb-2 mt-n2"></div>
                    <div class="row text-center">
                        <div class="col-6">
                            <p class="text-muted mb-2">This Week</p>
                            <h3 class="text-dark mb-3">23.5k</h3>
                        </div> <!-- end col -->
                        <div class="col-6">
                            <p class="text-muted mb-2">Last Week</p>
                            <h3 class="text-dark mb-3">41.05k</h3>
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                    <div class="text-center">
                        <button type="button" class="btn btn-light shadow-none w-100">View Details</button>
                    </div> 
                </div>
            </div>
        </div> <!-- end left chart card -->

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sessions by Country</h5>
                    <div id="ksa-map-markers" style="height: 316px">
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <p class="text-muted mb-2">This Week</p>
                            <h3 class="text-dark mb-3">23.5k</h3>
                        </div> <!-- end col -->
                        <div class="col-6">
                            <p class="text-muted mb-2">Last Week</p>
                            <h3 class="text-dark mb-3">41.05k</h3>
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div>
            </div> <!-- end card-->
        </div> <!-- end col -->

        <div class="col-lg-4">
            <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2">

                            <img src="{{ asset('images/default-avatar-female.svg') }}" alt="avatar" class="avatar rounded-circle" loading="lazy">

                            <div class="d-block">
                                <h4 class="text-dark fw-medium mb-1">{{ $topClient['name'] ?? 'No Data' }}</h4>
                                <p class="mb-0 text-muted">Client of Month</p>
                            </div>
                            
                        </div>
                        <div class="mt-4">
                            
                            <h3 class="fw-semibold mt-2 mb-0">SAR 4,700</h3>
                            <div id="clientofmonth" class="apex-charts mt-3"></div>
                        </div>
                    </div>
                    
                </div>
        </div> <!-- end col -->

        <div class="col-xl-4 d-none">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Recent Transactions</h4>
                    <div>
                        <a href="#!" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus me-1"></i>Add
                        </a>
                    </div>
                </div> <!-- end card-header-->
                <div class="card-body p-0">
                    <div class="px-3" data-simplebar style="max-height: 398px;">
                        <table class="table table-hover mb-0 table-centered">
                            <tbody>
                            <tr>
                                <td>24 April, 2024</td>
                                <td>$120.55</td>
                                <td><span class="badge bg-success">Cr</span></td>
                                <td>Commisions</td>
                            </tr>
                            <tr>
                                <td>24 April, 2024</td>
                                <td>$9.68</td>
                                <td><span class="badge bg-success">Cr</span></td>
                                <td>Affiliates</td>
                            </tr>
                            <tr>
                                <td>20 April, 2024</td>
                                <td>$105.22</td>
                                <td><span class="badge bg-danger">Dr</span></td>
                                <td>Grocery</td>
                            </tr>
                            <tr>
                                <td>18 April, 2024</td>
                                <td>$80.59</td>
                                <td><span class="badge bg-success">Cr</span></td>
                                <td>Refunds</td>
                            </tr>
                            <tr>
                                <td>18 April, 2024</td>
                                <td>$750.95</td>
                                <td><span class="badge bg-danger">Dr</span></td>
                                <td>Bill Payments</td>
                            </tr>
                            <tr>
                                <td>17 April, 2024</td>
                                <td>$455.62</td>
                                <td><span class="badge bg-danger">Dr</span></td>
                                <td>Electricity</td>
                            </tr>
                            <tr>
                                <td>17 April, 2024</td>
                                <td>$102.77</td>
                                <td><span class="badge bg-success">Cr</span></td>
                                <td>Interest</td>
                            </tr>
                            <tr>
                                <td>16 April, 2024</td>
                                <td>$79.49</td>
                                <td><span class="badge bg-success">Cr</span></td>
                                <td>Refunds</td>
                            </tr>
                            <tr>
                                <td>05 April, 2024</td>
                                <td>$980.00</td>
                                <td><span class="badge bg-danger">Dr</span></td>
                                <td>Shopping</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end card body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div> <!-- end row -->


@endsection

@section('script')
    @vite(['resources/js/pages/dashboard.js'])

@endsection
@section('script-bottom')
    @vite([
    'resources/js/components/form-flatepicker.js',  {{-- if you still need it elsewhere --}}
    'resources/js/pages/humanfd.js'
  ])
@endsection