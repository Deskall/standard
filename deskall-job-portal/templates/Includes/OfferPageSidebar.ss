<div class="sidebar">
	<div class="uk-margin">
		<strong><%t JobSearch.FilterTitle 'Ergebnisse filtern' %></strong>
		<div class="filters">
			<% loop filters %>
			<button class="uk-button uk-margin-small" data-filter-title="$Title">$Value<span class="uk-margin-small-left" data-uk-close></span></button>
			<% end_loop %>
		</div>
	</div>
	<% loop $CookConfig.Parameters.filter('ClassName','JobParameter') %>
	<div class="parameter uk-margin">
		<strong class="parameter-title">$Title</strong>
		<% loop $Values %>
		<% if $activeOffers.exists %>
		<div><a class="uk-flex uk-flex-between" data-filter="$Up.Title" data-filter-value="$Title" data-type="parameter"><span class="uk-text-truncate">$Title</span><span>$activeOffers.count</span></a></div>
		<% end_if %>
		<% end_loop %>
	</div>
	<% end_loop %>
	<div class="parameter uk-margin">
		<strong class="parameter-title"><%t JobSearch.Places 'Ort' %></strong>
		<% loop $CookConfig.activeCountries.GroupedBy(CountryTitle) %>
		<div><a class="uk-flex uk-flex-between" data-filter="Country" data-filter-value="$CountryTitle" data-type="data" data-sub="$CountryTitle"><span class="uk-text-truncate">$CountryTitle</span><span>$Children.count</span></a></div>
		<% end_loop %>
	</div>
	<% loop $CookConfig.activeCountries.GroupedBy(CountryTitle) %>
	<div data-parameter="$CountryTitle" class="sub-parameter uk-margin">
		<strong class="parameter-title"><%t JobSearch.Cities 'Stadt' %></strong>
		<% loop $Top.CookConfig.activeCities($CountryTitle).GroupedBy(CityTitle) %>
		<div><a class="uk-flex uk-flex-between" data-filter="City" data-filter-value="$CityTitle" data-type="data"><span class="uk-text-truncate">$CityTitle</span><span>$Children.count</span></a></div>
		<% end_loop %>
	</div>
	<% end_loop %>
	<div class="parameter uk-margin">
		<strong class="parameter-title"><%t JobSearch.Date 'Datum' %></strong>
		<% loop $CookConfig.activeDates.GroupedBy(PublishedPeriod) %>
		<div><a class="uk-flex uk-flex-between" data-filter="Date" data-filter-value="$PublishedPeriod" data-type="data"><span class="uk-text-truncate">$PublishedPeriod</span><span>$Children.count</span></a></div>
		<% end_loop %>
	</div>
</div>