<% if $IncludeFormTag %>
<form $AttributesHTML>
<% end_if %>
	<% if $Message %>
	<p id="{$FormName}_error" class="message $MessageType">$Message</p>
	<% else %>
	<p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
	<% end_if %>
	<div class="uk-child-width-1-1" data-uk-grid>
		<div>
			<% with Fields.FieldByName('Title') %>
			$FieldHolder
			<% end_with %>
		</div>
		<div>
			<% with Fields.FieldByName('ParametersFields') %>
			$FieldHolder
			<% end_with %>
		</div>
		<div>
			<% with Fields.FieldByName('City') %>
			$FieldHolder
			<% end_with %>
		</div>
		<div>
			<% with Fields.FieldByName('Country') %>
			$FieldHolder
			<% end_with %>
		</div>
		<div>
			<% with Fields.FieldByName('Description') %>
			$FieldHolder
			<% end_with %>
		</div>
		<div>
			<div id="Form_JobOfferForm_Image_Holder" class="field uk-margin-small">
				<label class="uk-form-label">Bild</label>
				<div class="uk-form-controls">
					<% if $Top.Record.ImageID > 0 %>
					<div class="switch-container-{$Top.Record.ID} original-container-{$Top.Record.ID} uk-position-relative">
						<img src="<% if $Top.Record.Image.getExtension == "svg" %>$Top.Record.Image.URL<% else %>$Top.Record.Image.ScaleWidth(300).URL<% end_if %>" class="switch-this">
						<div class="uk-position-top-right uk-text-center switch-this"><a data-uk-toggle="target:.switch-this" class="uk-text-large uk-display-block uk-padding-small uk-padding-remove-top"><i class="icon icon-edit"></i></a></div>
						<div id="upload-photo-container-{$Top.Record.ID}" class="js-upload with-preview uk-placeholder uk-text-center uk-margin-remove switch-this" data-container=".original-container-{$Top.Record.ID}" data-field-name="ImageID" hidden data-upload-url="{$Controller.MemberPage.Link}upload" data-allowed="*.(jpg|jpeg|gif|png)">
							<div class="form-field">
								<span data-uk-icon="icon: cloud-upload"></span>
								<span class="uk-text-middle"><%t Member.ChangePicture 'Legen Sie ein Bild ab oder' %></span>
								<div data-uk-form-custom>
									<input type="file" name="files">
									<span class="uk-link"><%t Member.SelectPicture 'Klicken Sie hier an' %></span>
								</div>
							</div>
							<div class="uk-position-top-right uk-dark uk-text-center">
								<a class="uk-text-large uk-display-block uk-padding-small uk-padding-remove-top" data-uk-toggle="target:.switch-this" ><i class="icon icon-close"></i></a>
							</div>
						</div>
					</div>
					<% else %>
					<div class="photo-profil-{$Top.Record.ID} uk-text-center">
						<div id="upload-photo-container-{$Top.Record.ID}" class="js-upload with-preview uk-placeholder uk-text-center uk-margin-remove" data-container=".photo-profil-{$Top.Record.ID}" data-field-name="ImageID" data-upload-url="{$Controller.MemberPage.Link}upload" data-allowed="*.(jpg|jpeg|gif|png)">
							<div class="form-field">
								<span data-uk-icon="icon: cloud-upload"></span>
								<span class="uk-text-middle"><%t Member.ChangePicture 'Legen Sie ein Bild ab oder' %></span>
								<div data-uk-form-custom>
									<input type="file" name="files">
									<span class="uk-link"><%t Member.SelectPicture 'Klicken Sie hier an' %></span>
								</div>
							</div>
							
						</div>
					</div>
					<% end_if %>
					<% with Fields.FieldByName('ImageID') %>
					$FieldHolder 
					<% end_with %>	
				</div>
			</div>
		</div>
		<div>
			<div id="Form_JobOfferForm_Files_Holder" class="field uk-margin-small">
				<label class="uk-form-label"><%t CustomUser.Files 'Dateien' %></label>
				<div class="uk-form-controls">
					<table class="uk-table uk-table-striped uk-table-small uk-table-middle">

						<tbody id="job-offer-files" data-uk-sortable>
							<% if $Top.Record.Attachments %>
							<% loop  $Top.Record.Attachments.sort('SortOrder') %>
							<tr><td class="uk-drag"><span class="icon icon-android-more-vertical"></span></td><td><i class="icon icon-file uk-text-large"></i></td><td>$Name</td><td><a data-delete-row><span class="icon icon-trash-a"></span></a></td><td><input type="hidden" name="TempFiles[]" value="$ID"/></td></tr>
							<% end_loop %>
							<% end_if %>
						</tbody>
					</table>
					<div class="uk-margin-small">
						<div id="upload-photos" class="js-upload multiple uk-placeholder uk-text-center" data-container="#job-offer-files" data-field-name="TempFiles[]" data-type="file" data-upload-url="{$Controller.MemberPage.Link}upload">
							<div class="form-field">
								<span data-uk-icon="icon: cloud-upload"></span>
								<span class="uk-text-middle"><%t Member.AddFiles 'Legen Sie Dateien ab oder' %></span>
								<div data-uk-form-custom>
									<% with Fields.FieldByName('Attachments') %>
									$Field
									<% end_with %>
									<span class="uk-link"><%t Member.SelectPicture 'Klicken Sie hier an' %></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<% with Fields.FieldByName('CustomerID') %>
	$FieldHolder
	<% end_with %>
	<% with Fields.FieldByName('SecurityID') %>
	$FieldHolder
	<% end_with %>
	<% if $Actions %>
	<div class="btn-toolbar">
		<% loop $Actions %>
			$Field
		<% end_loop %>
	</div>
	<% end_if %>
<% if $IncludeFormTag %>
</form>
<% end_if %>