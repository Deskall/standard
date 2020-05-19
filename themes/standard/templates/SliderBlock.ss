<% if HeaderSlide %>
  <% with HeaderSlide.Image %>
    <div class="dk-header-slide-home uk-flex uk-flex-center uk-flex-middle uk-background-cover uk-light" data-src="$FocusFill(350,350).URL" data-srcset="$FocusFill(350,350).URL 320w, $FocusFill(650,450).URL 650w, $FocusFill(1200,600).URL 1200w, $FocusFillMax(2000,750).URL 1500w" alt="" data-sizes="100vw" data-uk-img><% end_with %>
           
                <div class="uk-height-1-1 uk-width-1-1" data-uk-slideshow="autoplay:true;min-height: 350;animation: fade;">
                    <div class="uk-position-relative uk-height-1-1 uk-width-1-1" tab="-1">
                        <ul class="uk-slideshow-items">
                            <% loop ActiveSlides %>
                            <li class="uk-height-1-1" >
                                
                                    <div class="uk-height-1-1">
                                        <div class="uk-container uk-height-1-1 uk-position-relative">
                                            <div class="uk-position-top-left uk-position-z-index">
                                                <div class="dk-slide-text uk-text-left">
                                                    <div class="title">$Top.styledTitle($Title)</div>
                                                    <div class="slide-text">$Content</div>
                                                </div>
                                            </div>
                                            <% if LinkableLinkID > 0 %>
                                            <div class="uk-position-bottom-left uk-padding-large uk-padding-remove-horizontal">
                                                <% include CallToActionLink c=w,b=primary,pos=$LinkPosition %>
                                            </div>
                                            <% end_if %>
                                        </div>
                                    </div>
                                <% if $Image %>
                                <div class="uk-position-bottom-right">
                                  <img src="$Image.ScaleHeight(400).URL" />
                                </div>
                                <% end_if %>
                              
                            </li>
                            <% end_loop %>
                        </ul>
                        
                        <div class="uk-position-bottom-center uk-padding-small">
                            <ul class="uk-slideshow-nav uk-dotnav"></ul>
                        </div>
                    </div>
                </div>
            
    </div>
<% end_if %>