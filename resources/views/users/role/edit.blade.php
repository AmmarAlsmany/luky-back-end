@extends('layouts.vertical', ['title' => __('users.role_edit')])

@section('css')
@vite(['node_modules/choices.js/public/assets/styles/choices.min.css'])
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('users.roles_information') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form>
                            <div class="mb-3">
                                <label for="roles-name" class="form-label">{{ __('users.roles_name') }}</label>
                                <input type="text" id="roles-name" class="form-control" placeholder="{{ __('users.role_name_placeholder') }}" value="Workspace Manager">
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <form>
                            <div class="mb-3">
                                <label for="workspace" class="form-label">{{ __('users.add_workspace_label') }}</label>
                                <select class="form-control" id="workspace" data-choices data-choices-groups data-placeholder="{{ __('users.select_workspace') }}">
                                    <option value="">{{ __('users.facebook') }}</option>
                                    <option value="Slack">{{ __('users.slack') }}</option>
                                    <option value="Zoom">{{ __('users.zoom') }}</option>
                                    <option value="Analytics">{{ __('users.analytics') }}</option>
                                    <option value="Meet">{{ __('users.meet') }}</option>
                                    <option value="Mail">{{ __('users.mail') }}</option>
                                    <option value="Strip">{{ __('users.strip') }}</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="role-tag" class="form-label">{{ __('users.tag') }}</label>
                            <select class="form-control" id="choices-multiple-remove-button" data-choices data-choices-removeItem name="choices-multiple-remove-button" multiple>
                                <option value="Manager" selected>{{ __('users.manager') }}</option>
                                <option value="Product">{{ __('users.product') }}</option>
                                <option value="Data" selected>{{ __('users.data') }}</option>
                                <option value="Designer">{{ __('users.designer') }}</option>
                                <option value="Supporter">{{ __('users.supporter') }}</option>
                                <option value="System Design">{{ __('users.system_design') }}</option>
                                <option value="QA">{{ __('users.qa') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="user-name" class="form-label">{{ __('users.user_name') }}</label>
                            <input type="text" id="user-name" class="form-control" placeholder="{{ __('users.user_name_placeholder') }}" value="Gaston Lapierre ">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <p>{{ __('users.user_status') }} </p>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" checked="">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    {{ __('users.active') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                <label class="form-check-label" for="flexRadioDefault2">
                                    {{ __('users.in_active') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer border-top">
                <a href="#!" class="btn btn-primary">{{ __('users.save_change') }}</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
@vite(['resources/js/pages/app-ecommerce-product.js'])
@endsection