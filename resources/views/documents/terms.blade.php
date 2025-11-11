@extends('layouts.vertical', ['title' => 'Terms & Conditions'])

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card overflow-hidden" style="background: url('/images/small/img-2.jpg'); ">
            <div class="position-absolute top-0 end-0 bottom-0 start-0 bg-dark opacity-75"></div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-lg-7 text-center">
                        <h3 class="text-white">Terms &amp; Conditions</h3>
                        <p class="text-white-50">Client &amp; Provider terms for using the Luky platform</p>

                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <iconify-icon icon="solar:black-hole-bold-duotone" class="fs-28 text-primary"></iconify-icon>
                    <h3 class="mb-0 ms-2">Definitions</h3>
                </div>
                <p class="mb-0">
                    “Client” is a user booking services. “Provider” includes salons, makeup artists, hairstylists, and clinics.
                    “Booking” is a request for services made via the Luky app.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <iconify-icon icon="solar:black-hole-bold-duotone" class="fs-28 text-primary"></iconify-icon>
                    <h3 class="mb-0 ms-2">Client Terms</h3>
                </div>
                <ul class="mb-0">
                    <li>Provide accurate profile data and secure your account.</li>
                    <li>Bookings are confirmed only after timely payment post-approval.</li>
                    <li>Cancellation/refunds follow Admin-configured policy (e.g., % deduction if &gt;24h prior).</li>
                    <li>Attend on time; no-shows may incur fees and affect future use.</li>
                    <li>Use ratings/reviews responsibly; abusive content is prohibited.</li>
                    <li>Use in-app chat professionally for booking coordination only.</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <iconify-icon icon="solar:black-hole-bold-duotone" class="fs-28 text-primary"></iconify-icon>
                    <h3 class="mb-0 ms-2">Provider Terms</h3>
                </div>
                <ul class="mb-0">
                    <li>Maintain accurate business/permit details and working hours.</li>
                    <li>Respond promptly to requests; approvals open the payment window.</li>
                    <li>Honor approved &amp; paid bookings; keep availability updated.</li>
                    <li>Disclose prices, durations, and service details clearly.</li>
                    <li>Comply with local laws and Luky policies (no off-platform violations).</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <iconify-icon icon="solar:black-hole-bold-duotone" class="fs-28 text-primary"></iconify-icon>
                    <h3 class="mb-0 ms-2">Payments &amp; Fees</h3>
                </div>
                <p class="mb-0">
                    Payments via integrated gateways (e.g., MyFatoorah, Tabby, Tamara). Luky may charge commissions/fees as configured in Admin.
                    Refunds follow gateway rules and the cancellation policy.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <iconify-icon icon="solar:black-hole-bold-duotone" class="fs-28 text-primary"></iconify-icon>
                    <h3 class="mb-0 ms-2">Liability &amp; Disclaimers</h3>
                </div>
                <p class="mb-0">
                    Luky is a booking platform; services are provided by Providers. Luky isn’t responsible for service outcomes except as required by law.
                    Disputes are handled per applicable law; support may assist with mediation.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <iconify-icon icon="solar:black-hole-bold-duotone" class="fs-28 text-primary"></iconify-icon>
                    <h3 class="mb-0 ms-2">Changes</h3>
                </div>
                <p class="mb-0">
                    These terms may be updated; continued use signifies acceptance of the latest version.
                </p>
            </div>
        </div>

    </div>
</div>

@endsection
