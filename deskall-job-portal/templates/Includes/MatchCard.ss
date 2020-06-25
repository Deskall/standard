<div id="match-card-{$ID}" class="uk-card uk-card-default uk-card-body">
	<div class="uk-grid-small" data-uk-grid>
		<div class="uk-position-top-right"><span class="<% if Compatibility > 70 %>uk-label-success<% else_if Compatibility > 50 %>uk-label-warning<% else %>uk-label-danger<% end_if %> uk-padding-small">{$Compatibility}%</span>
		</div>
		<% if isVisible %>
		<% with Candidat %>
		<div class="uk-width-1-3">
			<img src="$Picture.URL" />
		</div>
		<div class="uk-width-2-3">
			<table>
				<tr><td>Name</td><td>$Member.FirstName $Member.Surname</td></tr>
				<tr><td>Email</td><td>$Member.Email</td></tr>
				<tr><td>Telefon</td><td>$Phone</td></tr>
			</table>
		</div>
		<div class="uk-width-1-1">
			<div class="uk-text-center">
				<a class="uk-button uk-button-primary" href="mailto:$Member.Email">Jetzt Kontakt aufnehmen</a>
			</div>
		</div>
		<% end_with %>
		<% else %>
		<div class="uk-width-1-3">
			<img src="https://via.placeholder.com/150x120" />
		</div>
		<div class="uk-width-2-3">
			<% with Candidat %>
			<div class="uk-card-title">$CurrentPosition</div>
			<table>
				<tr><td>Erfahrung</td><td>$ExperienceYears</td></tr>
				<tr><td>Sprachen</td><td>$Languages</td></tr>
			</table>
			<% end_with %>
		</div>
		<div class="uk-width-1-1">
			<div class="uk-text-center">
				<a class="uk-button uk-button-primary" data-contact-modal="$ID">Jetzt Kontakt erhalten</a>
			</div>
		</div>
		<% end_if %>
	</div>
</div>