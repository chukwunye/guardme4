{{--
 * JobClass - Geolocalized Job Board Script
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
--}}
@extends('layouts.master')
<?php
// Category
if ($ad->category) {
	$adCatParentId = $ad->category->parent_id;
} else {
	$adCatParentId = 0;
}
?>
@section('content')
	<div class="main-container">
		<div class="container">
			<div class="row">

				@if (count($errors) > 0)
					<div class="col-lg-12">
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<h5><strong>@lang('global.Oops ! An error has occurred. Please correct the red fields in the form')</strong></h5>
							<ul class="list list-check">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					</div>
				@endif

				@if (Session::has('flash_notification.message'))
					<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
						<div class="row">
							<div class="col-lg-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif

				<div class="col-md-9 page-content">
					<div class="inner-box category-content">
						<h2 class="title-2"><strong> <i class="icon-docs"></i> {{ t('Update My Ad') }}</strong></h2>
						<div class="row">
							<div class="col-sm-12">
								<form class="form-horizontal" id="createAd" method="POST" action="{{ lurl('update/' . $ad->id) }}"
									  enctype="multipart/form-data">
									{!! csrf_field() !!}
									<input type="hidden" name="ad_id" value="{{ $ad->id }}">
									<fieldset>
										<!-- Category -->
										<div class="form-group required <?php echo ($errors->has('category')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label">{{ t('Category') }} <sup>*</sup></label>
											<div class="col-md-8">
												<select name="category" id="category" class="form-control selecter">
													<option value="0" data-type=""
															@if(old('category', $ad->category_id)=='')selected="selected"@endif>
														{{ t('Select a category') }}
													</option>
													@foreach ($categories as $cat)
														<option value="{{ $cat->tid }}" data-type="{{ $cat->type }}"
																@if(old('category', $ad->category_id)==$cat->tid)selected="selected"@endif>
															{{ $cat->name }}
														</option>
													@endforeach
												</select>
											</div>
										</div>

										<!-- Job title -->
										<div class="form-group required <?php echo ($errors->has('title')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="title">{{ t('Title') }} <sup>*</sup></label>
											<div class="col-md-8">
												<input id="title" name="title" placeholder="{{ t('Job title') }}" class="form-control input-md"
													   type="text" value="{{ old('title', $ad->title) }}">
												<span class="help-block">{{ t('A great title needs at least 60 characters.') }} </span>
											</div>
										</div>

										<!-- Describe ad -->
										<div class="form-group required <?php echo ($errors->has('description')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="description">{{ t('Describe ad') }} <sup>*</sup></label>
											<div class="col-md-8">
												<textarea class="form-control" id="description" name="description" rows="10"
														  required="">{{ old('description', $ad->description) }}</textarea>
												<p class="help-block">{{ t('Describe what makes your ad unique') }}</p>
											</div>
										</div>

										<!-- Add Type -->
										<div id="adTypeBloc" class="form-group required <?php echo ($errors->has('ad_type')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label">{{ t('Job Type') }} <sup>*</sup></label>
											<div class="col-md-8">
												<select name="ad_type" id="ad_type" class="form-control selecter">
													@foreach ($ad_types as $type)
														<option value="{{ $type->tid }}" @if(old('ad_type', $ad->ad_type_id)==$type->tid)selected="selected"@endif>
															{{ $type->name }}
														</option>
													@endforeach
												</select>
											</div>
										</div>

										<!-- Salary -->
										<div id="salaryBloc" class="form-group <?php echo ($errors->has('salary_max')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="salary_max">{{ t('Salary') }}</label>
											<div class="col-md-4">
												<div class="input-group">
													@if ($country->get('currency')->in_left == 1)
														<span class="input-group-addon">{{ $country->get('currency')->symbol }}</span>
													@endif
													<input id="salary_min" name="salary_min" class="form-control" placeholder="{{ t('Salary (min)') }}" type="text" value="{{ old('salary_min', $ad->salary_min) }}">
													<input id="salary_max" name="salary_max" class="form-control" placeholder="{{ t('Salary (max)') }}" type="text" value="{{ old('salary_max', $ad->salary_max) }}">
													@if ($country->get('currency')->in_left == 0)
														<span class="input-group-addon">{{ $country->get('currency')->symbol }}</span>
													@endif
												</div>
											</div>

											<div class="col-md-4">
												<select name="salary_type" id="salary_type" class="form-control selecter">
													@foreach ($salary_type as $type)
														<option value="{{ $type->tid }}" @if(old('salary_type', $ad->salary_type_id)==$type->tid)selected="selected"@endif>
															{{ 'per'.' '.$type->name }}
														</option>
													@endforeach
												</select>
												<div class="checkbox">
													<label>
														<input id="negotiable" name="negotiable" type="checkbox"
															   value="1" {{ (old('negotiable', $ad->negotiable)=='1') ? 'checked="checked"' : '' }}>
														{{ t('Negotiable') }}
													</label>
												</div>
											</div>
										</div>

										<!-- Start Date -->
										<div class="form-group <?php echo ($errors->has('start_date')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="start_date">{{ t('Start Date') }} </label>
											<div class="col-md-8">
												<input id="start_date" name="start_date" placeholder="{{ t('Start Date') }}" class="form-control input-md"
													   type="text" value="{{ old('start_date', $ad->start_date) }}">
											</div>
										</div>



										<div class="content-subheading"><i class="icon-user fa"></i> <strong>Company information</strong></div>



										<!-- Company Name -->
										<div class="form-group required <?php echo ($errors->has('company_name')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="company_name">{{ t('Company Name') }} <sup>*</sup></label>
											<div class="col-md-8">
												<input id="company_name" name="company_name" placeholder="{{ t('Company Name') }}" class="form-control input-md" type="text" value="{{ old('company_name', $ad->company_name) }}">
											</div>
										</div>

										<!-- Company Description -->
										<div class="form-group required <?php echo ($errors->has('company_description')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="company_description">{{ t('Company Description') }} <sup>*</sup></label>
											<div class="col-md-8">
												<textarea class="form-control" id="company_description" name="company_description" rows="5">{{ old('company_description', $ad->company_description) }}</textarea>
												<p class="help-block">{{ t('Describe the company') }}</p>
											</div>
										</div>

										<!-- Logo -->
										<div id="logoBloc" class="form-group <?php echo ($errors->has('logo')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="logo"> {{ t('Logo') }} </label>
											<div class="col-md-8">
												<div class="mb10">
													<input id="logo" name="logo" type="file" class="file">
												</div>
												<p class="help-block">{{ t('Logo format') }}</p>
												<?php /*
												@if (trim($ad->logo) != '' and file_exists(public_path() . '/uploads/pictures/' . $ad->logo))
													<div>
														<a class="btn btn-default" href="{{ url('uploads/pictures/' . $ad->resume) }}" target="_blank">
															<img src="">
														</a>
													</div>
												@endif */
												?>
											</div>
										</div>

										<!-- Company Website -->
										<div class="form-group <?php echo ($errors->has('company_website')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="company_website">{{ t('Company Website') }} </label>
											<div class="col-md-8">
												<input id="company_website" name="company_website" placeholder="{{ t('Company Website') }}" class="form-control input-md" type="text" value="{{ old('company_website', $ad->company_website) }}">
											</div>
										</div>


										<div class="form-group required <?php echo ($errors->has('contact_name')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="contact_name">{{ t('Contact Name') }} <sup>*</sup></label>
											<div class="col-md-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="icon-user"></i></span>
													<input id="contact_name" name="contact_name" placeholder="{{ t('Contact Name') }}"
													   class="form-control input-md" type="text"
													   value="{{ old('contact_name', $ad->contact_name) }}">
												</div>
											</div>
										</div>

										<!-- Contact Email -->
										<div class="form-group required <?php echo ($errors->has('contact_email')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="contact_email"> {{ t('Contact Email') }} <sup>*</sup></label>
											<div class="col-md-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="icon-mail"></i></span>
													<input id="contact_email" name="contact_email" class="form-control"
														   placeholder="{{ t('Email') }}" type="text"
														   value="{{ old('contact_email', $ad->contact_email) }}">
												</div>
											</div>
										</div>

										<!-- Phone Number -->
										<div class="form-group required <?php echo ($errors->has('contact_phone')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="contact_phone">{{ t('Phone Number') }}</label>
											<div class="col-md-8">
												<div class="input-group">
													<span id="phone_country" class="input-group-addon">
														<i class="icon-phone-1"></i>
													</span>
													<input id="contact_phone" name="contact_phone"
														   placeholder="{{ t('Phone Number (in local format)') }}" class="form-control input-md"
														   type="text" value="{{ old('contact_phone', $ad->contact_phone) }}">
												</div>
												<div class="checkbox">
													<label>
														<input id="contact_phone_hidden" name="contact_phone_hidden" type="checkbox"
															   value="1" {{ (old('negotiable', $ad->contact_phone_hidden)=='1') ? 'checked="checked"' : '' }}>
														<small> {{ t('Hide the phone number on this ads.') }}</small>
													</label>
												</div>
											</div>
										</div>


										<!-- Button  -->
										<div class="form-group">
											<label class="col-md-3 control-label"></label>
											<div class="col-md-8">
												<button id="createAdBtn" class="btn btn-success btn-lg"> {{ t('Update') }} </button>
											</div>
										</div>

										<div style="margin-bottom: 30px;"></div>

									</fieldset>
								</form>

							</div>
						</div>
					</div>
				</div>
				<!-- /.page-content -->

				<div class="col-md-3 reg-sidebar">
					<div class="reg-sidebar-inner text-center">

						<div class="panel sidebar-panel">
							<div class="panel-heading uppercase">
								<small><strong>{{ t('How to sell quickly?') }}</strong></small>
							</div>
							<div class="panel-content">
								<div class="panel-body text-left">
									<ul class="list-check">
										<li> {{ t('Use a brief title and description of the item') }} </li>
										<li> {{ t('Make sure you post in the correct category') }}</li>
										<li> {{ t('Add a logo to your ad') }}</li>
										<li> {{ t('Put a min and max salary') }}</li>
										<li> {{ t('Check the item before publish') }}</li>
									</ul>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('javascript')
	@parent
	<script src="{{ url('assets/js/fileinput.min.js') }}" type="text/javascript"></script>
	@if (file_exists(public_path() . '/assets/js/fileinput_locale_'.$lang->get('abbr').'.js'))
		<script src="{{ url('/assets/js/fileinput_locale_'.$lang->get('abbr').'.js') }}" type="text/javascript"></script>
	@endif
	<script language="javascript">
		/* initialize with defaults (logo) */
		$('#logo').fileinput(
				{
					'showPreview': true,
					'allowedFileExtensions': ['jpg', 'jpeg', 'gif', 'png'],
					'browseLabel': '{!! t("Browse") !!}',
					'showUpload': false,
					'showRemove': false,
					'maxFileSize': 1000,
					@if (!is_null($ad->logo) and $ad->logo != '')
					initialPreview: [
						'<img src="<?php echo url('pic/x/cache/small/' . $ad->logo); ?>" class="file-preview-image" data-no-retina>',
					],
					/* initial preview configuration */
					initialPreviewConfig: [
						{
							caption: 'desert.jpg',
							width: '120px',
							url: '<?php echo url('ajax/pictures/delete/' . $ad->logo); ?>',
							key: <?php echo $ad->id; ?>,
							extra: {id: <?php echo $ad->id; ?>}
						}
					]
					@endif
				});
	</script>
	<script language="javascript">
		var lang = {
			'select': {
				'category': "{{ t('Select a category') }}",
				'subCategory': "{{ t('Select a sub-category') }}",
			},
			'price': "{{ t('Price') }}",
			'salary': "{{ t('Salary') }}"
		};
		var stepParam = 0;
		var category = <?php echo old('parent', intval($adCatParentId)); ?>;
		var categoryType = '<?php echo old('parent_type'); ?>';
		if (categoryType=='') {
			var selectedCat = $('select[name=parent]').find('option:selected');
			categoryType = selectedCat.data('type');
		}
		var subCategory = <?php echo old('category', intval($ad->category_id)); ?>;
		{{-- START / Fake fields to skip JS errors --}}
		var countryCode = '<?php echo old('country', $ad->country_code); ?>';
		var loc = '<?php echo old('location', 0); ?>';
		var subLocation = '<?php echo old('sub_location', 0); ?>';
		var city = '<?php echo old('city', 0); ?>';
		var hasChildren = '<?php echo old('has_children'); ?>';
		{{-- END / Fake fields to skip JS errors --}}
	</script>

	<script src="{{ url('/assets/js/app/d.select.category.js') }}"></script>
	<script src="{{ url('/assets/js/app/d.select.location.js') }}"></script>
@endsection
