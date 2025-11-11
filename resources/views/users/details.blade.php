@extends('layouts.vertical', ['title' => 'Users Details'])
@section('css')
@vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection
@section('content')
    <!-- Modal -->
    <div class="modal fade" id="exampleModalXl" tabindex="-1" aria-labelledby="exampleModalXlLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="exampleModalXlLabel">#VLZ002</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
            <div class="card-body">
                <!-- Logo & title -->
                <div class="clearfix pb-1  p-lg-2 p-2 m-n2 rounded position-relative" style="background: #B47EBA !important;">
                    <div class="float-sm-start">
                        <div class="auth-logo">
                            <img class="logo-dark me-1" src="/images/luky/logo.png" alt="logo-dark" height="75" />
                        </div>
                       
                    </div>
                    <div class="float-sm-end">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                     <tr>
                                        <td class="p-0 pe-5 py-1">
                                            <p class="mb-0 text-dark fw-semibold"> Invoice : </p>
                                        </td>
                                        <td class="text-end text-dark fw-semibold px-0 py-1">#INV-0758267/90</td>
                                    </tr>
                                    <tr>
                                        <td class="p-0 pe-5 py-1">
                                            <p class="mb-0 text-white">Status : </p>
                                        </td>
                                        <td class="text-end px-0 py-1"><span class="badge bg-success text-white  px-2 py-1 fs-13">Paid</span></td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="position-absolute top-100 start-50 translate-middle">
                        <!-- <img src="/images/luky/correct.png" alt="" class="img-fluid"> -->
                    </div>
                </div>

                <div class="clearfix pb-3 mt-4">
                    <div class="float-sm-start">
                        <div class="">
                            <h4 class="card-title">Vendor :</h4>
                            <div class="mt-3">
                                <h4>Noor Beauty Salon</h4>
                                <p class="mb-2">2437 Malaz Street ,Riyadh KSA</p>
                                <p class="mb-2"><span class="text-decoration-underline">Phone :</span> +(31)781-417-2004</p>
                                <p class="mb-2"><span class="text-decoration-underline">Email :</span> JulianeKuhn@jourrapide.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="float-sm-end">
                        <div class="">
                            <h4 class="card-title">Customer :</h4>
                            <div class="mt-3">
                                <h4>Rawan </h4>
                                <p class="mb-2">1344 Hershell Hollow Road WA 98168 , KSA</p>
                                <p class="mb-2"><span class="text-decoration-underline">Phone :</span> +(123) 732-760-5760</p>
                                <p class="mb-2"><span class="text-decoration-underline">Email :</span> hello@dundermuffilin.com</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive table-borderless text-nowrap table-centered">
                            <table class="table mb-0">
                                <thead class="bg-light bg-opacity-50">
                                    <tr>
                                        <th class="border-0 py-2">Service Name</th>
                                        <th class="border-0 py-2">Quantity</th>
                                        <th class="border-0 py-2">Price</th>
                                        <th class="border-0 py-2">Tax</th>
                                        <th class="border-0 py-2">Offer</th>
                                    
                                        <th class="text-end border-0 py-2">Total</th>
                                    </tr>
                                </thead> <!-- end thead -->
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded bg-primary avatar d-flex align-items-center justify-content-center">
                                                    <img src="/images/luky/nailpolisj.png" alt="" class="avatar">
                                                </div>
                                                <div>
                                                    <a href="#!" class="text-dark fw-medium fs-15">Nail Polish</a>
                                                    <p class="text-muted mb-0 mt-1 fs-13"><span>Service Place : </span>Home</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>1</td>
                                        <td>SAR 80.00</td>
                                        <td>SAR 15.00</td>
                                        <td>10%</td>
                                     
                                        <td class="text-end">$83.00</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded bg-primary avatar d-flex align-items-center justify-content-center">
                                                    <img src="/images/luky/shagcut.png" alt="" class="avatar">
                                                </div>
                                                <div>
                                                     <a href="#!" class="text-dark fw-medium fs-15">Shag Cut</a>
                                                    <p class="text-muted mb-0 mt-1 fs-13"><span>Service Place : </span>Home</p>
                                                    
                                                </div>
                                            </div>
                                        </td>
                                        <td>3</td>
                                         <td>SAR 100.00</td>
                                        <td>SAR 18.00</td>
                                        <td>15%</td>
                                       
                                        <td class="text-end">$330.00</td>
                                    </tr>
                                    
                                </tbody> <!-- end tbody -->
                            </table> <!-- end table -->
                        </div> <!-- end table responsive -->
                    </div> <!-- end col -->
                </div> <!-- end row -->

                <div class="row justify-content-end">
                    <div class="col-lg-5 col-6">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr class="">
                                        <td class="text-end p-0 pe-5 py-2">
                                            <p class="mb-0"> Sub Total : </p>
                                        </td>
                                        <td class="text-end text-dark fw-medium  py-2">$777.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end p-0 pe-5 py-2">
                                            <p class="mb-0">Discount : </p>
                                        </td>
                                        <td class="text-end text-dark fw-medium  py-2">-$60.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end p-0 pe-5 py-2">
                                            <p class="mb-0">Estimated Tax (15.5%) : </p>
                                        </td>
                                        <td class="text-end text-dark fw-medium  py-2">$20.00</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="text-end p-0 pe-5 py-2">
                                            <p class="mb-0 text-dark fw-semibold">Grand Amount : </p>
                                        </td>
                                        <td class="text-end text-dark fw-semibold  py-2">$737.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

                <!-- <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="alert alert-danger alert-icon p-2" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded bg-danger d-flex justify-content-center align-items-center fs-18 me-2 flex-shrink-0">
                                    <i class="bx bx-info-circle text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    All accounts are to be paid within 7 days from receipt of invoice. To be paid by cheque or credit card or direct payment online. If account is not paid within 7 days the credits details supplied as confirmation of work undertaken will be charged the agreed quoted fee noted above.
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="mt-3 mb-1">
                    <div class="text-end d-print-none">
                        <a href="javascript:window.print()" class="btn btn-info width-xl">Print</a>
                        
                    </div>
                </div>

            </div> <!-- end card body -->
        </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Edit User Info Modal -->
    <div class="modal fade" id="userEditInfo" tabindex="-1" aria-labelledby="userEditInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="userEditInfoLabel">Edit User Information</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <!-- Quick summary (read-only display) -->


            <!-- Editable form -->
            <form id="userEditForm" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="userName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="userName" required value="Michael A. Miner">
                <div class="invalid-feedback">Please enter a name.</div>
            </div>

            <div class="mb-3">
                <label for="userEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="userEmail" required value="michaelaminer@dayrep.com">
                <div class="invalid-feedback">Please enter a valid email.</div>
            </div>

            <div class="mb-3">
                <label for="userPhone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="userPhone" required value="+28 (57) 760-010-27">
                <div class="invalid-feedback">Please enter a phone number.</div>
            </div>

            <div class="mb-0">
                <label for="userLocation" class="form-label">Location</label>
                <input type="text" class="form-control" id="userLocation" required value="Riyadh, Saudi Arabia">
                <div class="invalid-feedback">Please enter a location.</div>
            </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button id="saveUserBtn" type="button" class="btn btn-primary">Save changes</button>
        </div>
        </div>
    </div>
    </div>

 
    <!-- Chat Message Modal (clean layout) -->
    <div class="modal fade" id="chatMessageModal" tabindex="-1" aria-labelledby="chatMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow-sm rounded-3">
            <form id="chatMessageForm" novalidate>
                <div class="modal-header py-3">
                <div>
                    <h6 class="modal-title mb-0" id="chatMessageLabel">Send a Message</h6>
                    <small class="text-muted">A quick note to the user</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-3">
                <!-- Floating textarea (clean + professional) -->
                <div class="form-floating">
                    <textarea class="form-control" placeholder="Write your message here" id="chatMessageText"
                            style="height: 140px" maxlength="500" required></textarea>
                    <label for="chatMessageText">Your message</label>
                </div>

                <div class="d-flex align-items-center mt-2">
                    <small class="text-muted me-auto">
                    Press <kbd>Enter</kbd> to send • <kbd>Shift</kbd>+<kbd>Enter</kbd> for a new line
                    </small>
                    <small class="text-muted"><span id="msgCharCount">0</span>/500</small>
                </div>
                </div>

                <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button id="chatSendBtn" type="submit" class="btn btn-primary" >
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="msgSpinner" role="status" aria-hidden="true"></span>
                    Send
                </button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Transaction Logs Modal -->
<div class="modal fade" id="transactionLogsModal" tabindex="-1" aria-labelledby="transactionLogsLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl"><!-- wide for table -->
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0" id="transactionLogsLabel">Transaction Logs</h5>
          <small class="text-muted">Review recent invoices and payment status</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- Your card dropped in here, with Action column removed -->
        <div class="card border-0 shadow-sm">
          <div class="d-flex card-header justify-content-between align-items-center">
            <div>
              <h4 class="card-title mb-0">All Invoices List</h4>
            </div>
            <div class="dropdown">
              <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light rounded" data-bs-toggle="dropdown" aria-expanded="false">
                This Month
              </a>
              <div class="dropdown-menu dropdown-menu-end">
                <a href="#!" class="dropdown-item">Download</a>
                <a href="#!" class="dropdown-item">Export</a>
                <a href="#!" class="dropdown-item">Import</a>
              </div>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0 table-hover table-centered">
                <thead class="bg-light-subtle">
                  <tr>
                    <th style="width: 20px;">
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheckAll">
                        <label class="form-check-label" for="customCheckAll"></label>
                      </div>
                    </th>
                    <th>Invoice ID</th>
                    <th>Billing Name</th>
                    <th>Order Date</th>
                    <th>Total</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <!-- Action column removed -->
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck2">
                        <label class="form-check-label" for="customCheck2">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV2540</a></td>
                    <td><img src="/images/users/avatar-2.jpg" class="avatar-sm rounded-circle me-2" alt=""> Michael A. Miner</td>
                    <td>07 Jan, 2023</td>
                    <td>$452</td>
                    <td>Mastercard</td>
                    <td><span class="badge bg-success-subtle text-success py-1 px-2">Completed</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck3">
                        <label class="form-check-label" for="customCheck3">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV3924</a></td>
                    <td><img src="/images/users/avatar-3.jpg" class="avatar-sm rounded-circle me-2" alt=""> Theresa T. Brose</td>
                    <td>03 Dec, 2023</td>
                    <td>$783</td>
                    <td>Visa</td>
                    <td><span class="badge bg-danger-subtle text-danger px-2 py-1">Cancel</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck4">
                        <label class="form-check-label" for="customCheck4">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV5032</a></td>
                    <td><img src="/images/users/avatar-4.jpg" class="avatar-sm rounded-circle me-2" alt=""> James L. Erickson</td>
                    <td>28 Sep, 2023</td>
                    <td>$134</td>
                    <td>Paypal</td>
                    <td><span class="badge bg-success-subtle text-success py-1 px-2">Completed</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck5">
                        <label class="form-check-label" for="customCheck5">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV1695</a></td>
                    <td><img src="/images/users/avatar-5.jpg" class="avatar-sm rounded-circle me-2" alt=""> Lily W. Wilson</td>
                    <td>10 Aug, 2023</td>
                    <td>$945</td>
                    <td>Mastercard</td>
                    <td><span class="badge bg-primary-subtle text-primary py-1 px-2">Pending</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck6">
                        <label class="form-check-label" for="customCheck6">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV8473</a></td>
                    <td><img src="/images/users/avatar-6.jpg" class="avatar-sm rounded-circle me-2" alt=""> Sarah M. Brooks</td>
                    <td>22 May, 2023</td>
                    <td>$421</td>
                    <td>Visa</td>
                    <td><span class="badge bg-danger-subtle text-danger px-2 py-1">Cancel</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck7a">
                        <label class="form-check-label" for="customCheck7a">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV2150</a></td>
                    <td><img src="/images/users/avatar-7.jpg" class="avatar-sm rounded-circle me-2" alt=""> Joe K. Hall</td>
                    <td>15 Mar, 2023</td>
                    <td>$251</td>
                    <td>Paypal</td>
                    <td><span class="badge bg-success-subtle text-success py-1 px-2">Completed</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck7b">
                        <label class="form-check-label" for="customCheck7b">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV5636</a></td>
                    <td><img src="/images/users/avatar-8.jpg" class="avatar-sm rounded-circle me-2" alt=""> Ralph Hueber</td>
                    <td>15 Mar, 2023</td>
                    <td>$310</td>
                    <td>Visa</td>
                    <td><span class="badge bg-success-subtle text-success py-1 px-2">Completed</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck7c">
                        <label class="form-check-label" for="customCheck7c">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV2940</a></td>
                    <td><img src="/images/users/avatar-9.jpg" class="avatar-sm rounded-circle me-2" alt=""> Sarah Drescher</td>
                    <td>15 Mar, 2023</td>
                    <td>$241</td>
                    <td>Mastercard</td>
                    <td><span class="badge bg-success-subtle text-success py-1 px-2">Completed</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck7d">
                        <label class="form-check-label" for="customCheck7d">&nbsp;</label>
                      </div>
                    </td>
                    <td><a href="javascript:void(0);" class="text-body">#INV9027</a></td>
                    <td><img src="/images/users/avatar-10.jpg" class="avatar-sm rounded-circle me-2" alt=""> Leonie Meister</td>
                    <td>15 Mar, 2023</td>
                    <td>$136</td>
                    <td>Paypal</td>
                    <td><span class="badge bg-primary-subtle text-primary py-1 px-2">Pending</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
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
        <!-- /card -->
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="bg-primary profile-bg rounded-top p-5 position-relative mx-n3 mt-n3">
                        <img src="/images/users/avatar-2.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle position-absolute top-100 start-0 translate-middle ms-5">
                    </div>
                    <div class="mt-4 pt-3">
                        <h4 class="mb-1"> Michael A. Miner<i class="bx bxs-badge-check text-success align-middle"></i></h4>
                        <div class="mt-2">
                           
                            <p class="fs-15 mb-1 mt-1"><span class="text-dark fw-semibold">Email : </span> michaelaminer@dayrep.com</p>
                            <p class="fs-15 mb-0 mt-1"><span class="text-dark fw-semibold">Phone : </span> +28 (57) 760-010-27</p>
                            <p class="fs-15 mb-0 mt-1"><span class="text-dark fw-semibold">Location : </span> Riyadh, Saudi Arabia</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top gap-1 hstack">
                    <a href="#!" class="btn btn-primary w-100"  data-bs-toggle="modal" data-bs-target="#chatMessageModal">Send Message</a>
                    <!-- <a href="#!" class="btn btn-light w-100" data-bs-toggle="modal" data-bs-target="#transactionLogsModal">Transactions logs</a> -->
                    <a href="#!" class="btn btn-soft-dark d-inline-flex align-items-center justify-content-center rounded avatar-sm" data-bs-toggle="modal" data-bs-target="#userEditInfo"><i class='bx bx-edit-alt fs-18'></i></a>
                    <a href="http://127.0.0.1:8000/users/details" class="btn btn-soft-dark d-inline-flex align-items-center justify-content-center rounded avatar-sm" ><i class='bx bx-message fs-18'></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-8">
            <div class="row">
                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="card-title mb-2 d-flex align-items-center gap-2">Total Service Requests </h4>
                                    <p class="text-muted fw-medium fs-22 mb-0">472</p>
                                </div>
                                <div>
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                        <iconify-icon icon="solar:bag-4-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="card-title mb-2 d-flex align-items-center gap-2">Fulfilled Services </h4>
                                    <p class="text-muted fw-medium fs-22 mb-0">123 </p>
                                </div>
                                <div>
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                        <iconify-icon icon="solar:bag-smile-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="card-title mb-2 d-flex align-items-center gap-2">Pending Services</h4>
                                    <p class="text-muted fw-medium fs-22 mb-0">231</p>
                                </div>
                                <div>
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                        <iconify-icon icon="solar:sort-by-time-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="card-title mb-2 d-flex align-items-center gap-2">Rejected Services</h4>
                                    <p class="text-muted fw-medium fs-22 mb-0">367</p>
                                </div>
                                <div>
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                        <iconify-icon icon="solar:bag-cross-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- favourite  -->
                 <div class="col-md-12 col-xl-12">
                    <div class="card">
                         <div class="card-header border-bottom-dashed">
                            <h4 class="card-title mb-0">Most-Visited Salons</h4>
                        </div> 
                        <div class="card-body">

                            <div class="row gy-2">
                                <div class="col-lg-4 col-4">
                                    <div class="d-flex align-items-center gap-2 border-end">
                                        <div class="">
                                             <iconify-icon icon="solar:cup-star-bold-duotone" class="fs-28 text-primary"></iconify-icon>

                                        </div>
                                        <div>
                                            <h5 class="mb-1">Instant Elegance Salon</h5>
                                            <p class="mb-0">Riyadh</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-4">
                                    <div class="d-flex align-items-center gap-2 border-end">
                                        <div class="">
                                            <iconify-icon icon="solar:cup-star-bold-duotone" class="fs-28 text-primary"></iconify-icon>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Yoush Beauty Salon</h5>
                                            <p class="mb-0">Riyadh</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="">
                                            <iconify-icon icon="solar:cup-star-bold-duotone" class="fs-28 text-primary"></iconify-icon>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Vida Salon</h5>
                                            <p class="mb-0">Riyadh</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- favourite  -->
            </div>
        </div>



    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">All Services</h4>
                    </div>
                    <div>
                        
                        <!-- <input type="date" id="range-datepicker" class="form-control" placeholder="Select date" data-provider="flatpickr" data-range="true" data-date-format="d M, Y" /> -->
                        <input type="text" id="humanfd-datepicker" class="form-control" placeholder="Select Date">
                    </div>


                </div>
                            <div class="card">
                <div class="card-body">


                    <div class="">
                        <ul class="nav nav-pills bg-transparent">
                            <li class="nav-item">
                                <a href="#homePill" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                    <span class="d-block d-sm-none"><i class="bx bx-home"></i></span>
                                    <span class="d-none d-sm-block">All</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#profilePill" data-bs-toggle="tab" aria-expanded="true" class="nav-link ">
                                    <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                                    <span class="d-none d-sm-block">Completed</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#messagesPill" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                    <span class="d-block d-sm-none"><i class="bx bx-envelope"></i></span>
                                    <span class="d-none d-sm-block">Canceled</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#messagesPill" data-bs-toggle="tab" aria-expanded="false" class="nav-link ">
                                    <span class="d-block d-sm-none"><i class="bx bx-envelope"></i></span>
                                    <span class="d-none d-sm-block">Pending</span>
                                </a>
                            </li>
                        </ul>
                     
                    </div>

               
                </div>
            </div>
                <div class="card-body p-0">


                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Provider</th>
                                    <th>Time</th>
                                    <th>Date</th>
                                    <th>Charges</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#VLZ001</td>
                                    <td>Hand Care</td>
                                    <td> Yousef Saloon</td>
                                    <td> 01:45 PM</td>
                                    <td> 07 Jan, 2023</td>
                                    <td>SAR 289.00</td>
                                    <td><span class="badge bg-success-subtle text-success py-1 px-2">Completed</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#!" class="btn btn-light btn-sm"  data-bs-toggle="modal"  data-bs-target="#exampleModalXl"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                        
                                            <a href="#!" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>#VLZ002</td>   
                                    <td>Massage</td>
                                    <td> Yousef Saloon</td>
                                    <td> 01:45 PM</td>
                                    <td> 03 Dec, 2023</td>
                                    <td>SAR 213.00</td>
                                    <td><span class="badge bg-danger-subtle text-danger py-1 px-2">Cancel</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#!" class="btn btn-light btn-sm"  data-bs-toggle="modal"  data-bs-target="#exampleModalXl"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                        
                                            <a href="#!" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>#VLZ002</td>   
                                    <td>Eye Care</td>
                                    <td> Yousef Saloon</td>
                                    <td> 01:45 PM</td>
                                    <td> 28 Sep, 2023 </td>
                                    <td>SAR 735.00</td>
                                    <td><span class="badge bg-success-subtle text-success py-1 px-2">Completed</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#!" class="btn btn-light btn-sm"   data-bs-toggle="modal"  data-bs-target="#exampleModalXl"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                        
                                            <a href="#!" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#VLZ002</td>   
                                    <td>Hair Cutting</td>
                                    <td> Yousef Saloon</td>
                                    <td> 01:45 PM</td>
                                    <td>10 Aug, 2023</td>
                                    <td>SAR 324.00</td>
                                    <td><span class="badge bg-warning-subtle text-warning py-1 px-2">Pending</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#!" class="btn btn-light btn-sm"   data-bs-toggle="modal"  data-bs-target="#exampleModalXl"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                            
                                            <a href="#!" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>#VLZ002</td>   
                                    <td>Nail Polish</td>
                                    <td> Yousef Saloon</td>
                                    <td> 01:45 PM</td>
                                    <td>19 Dec, 2023</td>
                                    <td>SAR 521.00</td>
                                    <td><span class="badge bg-warning-subtle text-warning py-1 px-2">Pending</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#!" class="btn btn-light btn-sm"  data-bs-toggle="modal"  data-bs-target="#exampleModalXl"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>

                                            <a href="#!" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        </div>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                </div>
        <div class="row g-0 align-items-center justify-content-between text-center text-sm-start p-3 border-top">
            <div class="col-sm">
                <div class="text-muted">
                    Showing <span class="fw-semibold">10</span> of <span class="fw-semibold">59</span> Results
                </div>
            </div>
            <div class="col-sm-auto mt-3 mt-sm-0">
                <ul class="pagination  m-0">
                    <li class="page-item">
                        <a href="#" class="page-link"><i class='bx bx-left-arrow-alt'></i></a>
                    </li>
                    <li class="page-item active">
                        <a href="#" class="page-link">1</a>
                    </li>
                    <li class="page-item">
                        <a href="#" class="page-link">2</a>
                    </li>
                    <li class="page-item">
                        <a href="#" class="page-link">3</a>
                    </li>
                    <li class="page-item">
                        <a href="#" class="page-link"><i class='bx bx-right-arrow-alt'></i></a>
                    </li>
                </ul>
            </div>
        </div>
            </div>
        </div>



    </div>

@endsection
@section('script-bottom')
  @vite([
    'resources/js/components/form-flatepicker.js',  {{-- if you still need it elsewhere --}}
    'resources/js/pages/humanfd.js'
  ])
@endsection