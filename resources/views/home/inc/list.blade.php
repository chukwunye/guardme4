@if (isset($latestAds->rows) and !empty($latestAds->rows))
<div class="row">

	<div class="col-sm-12 page-content col-thin-right">
		<div class="content-box col-lg-12">
			<div class="row row-featured row-featured-category">
				<div class="col-lg-12 box-title no-border">
					<div class="inner">
						<h2>
							<span class="title-3">{!! $latestAds->title !!}</span>
							<a href="{{ $latestAds->link }}" class="sell-your-item">
								{{ t('View more') }} <i class="icon-th-list"></i>
							</a>
						</h2>
					</div>
				</div>

				<div class="adds-wrapper jobs-list">

					@foreach($latestAds->rows as $key => $ad)
						<?php
						// Ads URL setting
						$adUrl = lurl(slugify($ad->title) . '/' . $ad->id . '.html');

						// Logo setting
						$adLogo = '';
						if (!empty($ad->logo)) {
							if (is_file(public_path() . '/uploads/pictures/'. $ad->logo)) {
								$adLogo = url('pic/x/cache/medium/' . $ad->logo);
							}
							if ($adLogo=='') {
								if (is_file(public_path() . '/'. $ad->logo)) {
									$adLogo = url('pic/x/cache/medium/' . $ad->logo);
								}
							}
						}
						// Default picture
						if ($adLogo=='') {
							$adLogo = url('pic/x/cache/medium/' . config('larapen.core.picture'));
						}
						?>
					<div class="item-list job-item">
						<div class="col-sm-1 col-xs-2 no-padding photobox">
							<div class="add-image">
								<a href="{{ $adUrl }}">
									<img alt="{{ mb_ucfirst($ad->company_name) }}" src="{{ $adLogo }}" class="thumbnail no-margin" data-no-retina>
								</a>
							</div>
						</div>
						<!--/.photobox-->
						<div class="col-sm-10 col-xs-10  add-desc-box">
							<div class="add-details jobs-item">
								<h5 class="company-title ">
									<a href="{{ lurl(trans('routes.v-search-company', ['countryCode' => $country->get('icode'), 'companyName' => $ad->company_name])) }}">
										{{ mb_ucfirst($ad->company_name) }}
									</a>
								</h5>
								<h4 class="job-title">
									<a href="{{ $adUrl }}">
										{{ mb_ucfirst($ad->title) }}
									</a>
								</h4>
								<?php
								// Convert the created_at date to Carbon object
								$ad->created_at = \Carbon\Carbon::parse($ad->created_at)->timezone(session('time_zone'));
								$ad->created_at = time_ago($ad->created_at, session('time_zone'), session('language_code'));
								?>
								<span class="info-row">
									<span class="date">
										<i class="icon-clock"> </i>
										{{ $ad->created_at }}
									</span>
									<span class="item-location">
										<i class="fa fa-map-marker"></i>
										{{ \App\Larapen\Models\City::find($ad->city_id)->name }}
									</span>
									<span class="date">
										<i class=" icon-clock"> </i>
										{{ \App\Larapen\Models\AdType::transById($ad->ad_type_id)->name }}
									</span>
									<span class=" salary">
										<i class=" icon-money"> </i>
										@if ($ad->salary_max > 0)
											@if ($country->get('currency')->in_left == 1)
												{{ $country->get('currency')->symbol.\App\Larapen\Helpers\Number::short($ad->salary_min) }} -
												{{ $country->get('currency')->symbol.\App\Larapen\Helpers\Number::short($ad->salary_max) }}
											@else
												{{ \App\Larapen\Helpers\Number::short($ad->salary_min).$country->get('currency')->symbol }}
												{{ \App\Larapen\Helpers\Number::short($ad->salary_max).$country->get('currency')->symbol }}
											@endif
										@else
											@if ($country->get('currency')->in_left == 1){{ $country->get('currency')->symbol }}@endif
											{{ '--' }}
											@if ($country->get('currency')->in_left == 0){{ $country->get('currency')->symbol }}@endif
										@endif
										{{ t('a') }} {{ \App\Larapen\Models\SalaryType::transById($ad->salary_type_id)->name }}
									</span>
								</span>

								<div class="jobs-desc">
									{{ str_limit($ad->description, 180) }}
								</div>

								<div class="job-actions">
									<ul class="list-unstyled list-inline">
										@if (Auth::check())
											@if (\App\Larapen\Models\SavedAd::where('user_id', $user->id)->where('ad_id', $ad->id)->count() <= 0)
												<li id="{{ $ad->id }}">
													<a class="save-job" id="save-{{ $ad->id }}">
														<span class="fa fa-star-o"></span>
														{{ t('Save Job') }}
													</a>
												</li>
											@else
												<li class="saved-job" id="{{ $ad->id }}">
													<a class="saved-job" id="saved-{{ $ad->id }}">
														<span class="fa fa-star"></span>
														{{ t('Saved Job') }}
													</a>
												</li>
											@endif
										@else
											<li id="{{ $ad->id }}">
												<a class="save-job" id="save-{{ $ad->id }}">
													<span class="fa fa-star-o"></span>
													{{ t('Save Job') }}
												</a>
											</li>
										@endif
										<li>
											<a class="email-job" data-toggle="modal" data-id="{{ $ad->id }}" href="#sendByEmail" id="email-{{ $ad->id }}">
												<i class="fa fa-envelope"></i>
												{{ t('Email Job') }}
											</a>
										</li>
									</ul>
								</div>

							</div>
						</div>
						<!--/.add-desc-box-->

						<!--/.add-desc-box-->
					</div>
					<!--/.job-item-->
					@endforeach

				</div>

				<div class="tab-box  save-search-bar text-center">
					<a class="text-uppercase" href="{{ lurl(trans('routes.v-search', ['countryCode' => $country->get('icode')])) }}">
						<i class=" icon-briefcase "></i>
						{{ t('View all jobs') }}
					</a>
				</div>
			</div>

		</div>
	</div>

</div>
@section('modal-location')
	@parent
	@include('layouts.inc.modal.send-by-email')
@endsection
@section('javascript')
	@parent
	<script>
		/* JS translation */
		var lang = {
			loginToSaveAd: "@lang('global.Please log in to save the Ads.')",
			loginToSaveSearch: "@lang('global.Please log in to save your search.')",
			confirmationSaveSearch: "@lang('global.Search saved successfully !')",
			confirmationRemoveSaveSearch: "@lang('global.Search deleted successfully !')"
		};
		$(document).ready(function ()
		{
			/* Get Ad ID */
			$('.email-job').click(function(){
				var adId = $(this).attr("data-id");
				$('input[type=hidden][name=ad]').val(adId);
			});

			@if (count($errors) > 0)
				@if (old('sendByEmailForm')=='1')
					$('#sendByEmail').modal();
			@endif
			@endif
		})
	</script>
	<script src="{{ url('assets/js/form-validation.js') }}"></script>
	<script type="text/javascript" src="{{ url('assets/js/app/make.favorite.js') }}"></script>
@endsection
@endif