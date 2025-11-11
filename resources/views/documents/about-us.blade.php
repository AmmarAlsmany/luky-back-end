@extends('layouts.vertical', ['title' => 'About Us'])

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-12">
    <div class="card overflow-hidden" style="background: url('/images/small/img-4.jpg');">
      <div class="position-absolute top-0 end-0 bottom-0 start-0 bg-dark opacity-75"></div>
      <div class="card-body">
        <div class="row justify-content-center">
          <div class="col-lg-7 text-center">
            <h3 class="text-white">About Luky</h3>
            <p class="text-white-50 mb-0">Smart, simple booking for beauty and cosmetic services.</p>
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
        <h4>Introduction to the App</h4>
        <p class="mb-0">
          Luky connects clients with service providers (Salons, Makeup Artists, Hairstylists, Clinics) through a professional experience that
          simplifies booking, organizes schedules, and improves access to beauty services.
        </p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h4>Objectives</h4>
        <ul class="mb-0">
          <li>Facilitate booking between clients and providers.</li>
          <li>Organize provider schedules and availability.</li>
          <li>Improve efficiency and discovery of services by city.</li>
        </ul>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h4>Platforms & Languages</h4>
        <ul class="mb-0">
          <li>Operating Systems: iOS and Android</li>
          <li>App Languages: Arabic and English</li>
        </ul>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h4>Components</h4>
        <ol class="mb-0">
          <li>Client App</li>
          <li>Service Provider App</li>
          <li>Admin Dashboard</li>
        </ol>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h4>How Luky Works</h4>
        <p class="mb-2">Clients can browse providers by city, view profiles and services, select a date/time, and submit a booking request. Providers approve or reject; upon approval, clients complete payment to confirm.</p>
        <ul class="mb-0">
          <li>Location-based filtering (with manual city selection).</li>
          <li>Dynamic availability calendar (available/unavailable/reserved states).</li>
          <li>Secure online payments and promo codes.</li>
          <li>Post-booking ratings that improve provider visibility.</li>
        </ul>
      </div>
    </div>

  </div>
</div>
@endsection
