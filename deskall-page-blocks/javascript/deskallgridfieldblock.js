//Custom Javascript Function Block
(function($) {
    $.entwine("ss", function($) {
        // $(".ss-gridfield-add-new-multi-class select[name='BlockType']").entwine({
        //      onadd: function() {
        //          this.update();
        //      },
        //      onchange: function() {
        //          this.update();
        //      },
        //      update: function() {
        //          var btn = this.parents(".ss-gridfield-add-new-multi-class").find(".ss-ui-button");
        //          var link = btn.data("href");
        //          var cls  = btn.parents(".ss-gridfield-add-new-multi-class").find("select[name='BlockType']").val();
        //          if(cls && cls.length){
        //             if (cls == "DuplicateBlock"){
        //                 $(".ss-gridfield-duplicate-block").css({"display":"inline-block"});
        //             }
        //             else{
        //                 $(".ss-gridfield-duplicate-block").css({"display":"none"});
        //                 btn.getGridField().showDetailView(link.replace("/{id}/{pageid}", '').replace("{class}", cls));
        //             }
        //          }
        //      }
        // });

        
        $(".ss-gridfield-add-new-multi-class select[name='GridFieldAddNewMultiClass[ClassName]']").entwine({
            onchange: function(){
                this._super();
                if (!this.parents('form').find('.ss-gridfield-duplicate-block').hasClass("dk-hidden")){
                    this.parents('form').find('.ss-gridfield-duplicate-block').addClass("dk-hidden");
                }
                if (!this.parents('form').find('.ss-gridfield-link-block').hasClass("dk-hidden")){
                    this.parents('form').find('.ss-gridfield-link-block').addClass("dk-hidden");
                }
                if (this.val() == "DuplicateBlock"){
                    this.parents('form').find('.ss-gridfield-duplicate-block').removeClass('dk-hidden');
                    this.parents('.ss-gridfield-add-new-multi-class').find('[data-add-multiclass]').hide();
                }
                else if (this.val() == "VirtualBlock"){
                    this.parents('form').find('.ss-gridfield-link-block').removeClass('dk-hidden');
                    this.parents('.ss-gridfield-add-new-multi-class').find('[data-add-multiclass]').hide();
                }
                else {
                    if (!this.parents('form').find('.ss-gridfield-duplicate-block').hasClass('dk-hidden')){
                        this.parents('form').find('.ss-gridfield-duplicate-block').addClass('dk-hidden');
                    }
                    if (!this.parents('form').find('.ss-gridfield-link-block').hasClass('dk-hidden')){
                        this.parents('form').find('.ss-gridfield-link-block').addClass('dk-hidden');
                    }
                    this.parents('.ss-gridfield-add-new-multi-class').find('[data-add-multiclass]').show();
                }
            }
        });
        //Duplicate Block
        $(".ss-gridfield-duplicate-block select[name='Block']").entwine({
             onadd: function() {
                 this.update();
             },
             onchange: function() {
                 this.update();
             },
             update: function() {
                 var btn = this.parents(".ss-gridfield-duplicate-block").find(".btn-duplicate-block");
                 var link = btn.data("href");
                 var block = btn.parents(".ss-gridfield-duplicate-block").find("select[name='Block']").val();
                 if(block && block > 0) {
                    btn.removeClass("disabled");
                    var path = window.location.pathname;
                    //if not in page but in parent block
                    if (path.indexOf("/edit/show/") == - 1){
                        var urlsegment = path.substr(path.indexOf("/edit/EditForm/") + 15);
                        var page = urlsegment.substr(0, urlsegment.indexOf("/"));
                    }
                    else{
                        var page = path.substr(path.indexOf("/edit/show/") + 11);
                    }
                    
                    var finallink = link.replace("{id}", block).replace("{pageid}",page);
                    btn.getGridField().showDetailView(finallink);
                }
                else{
                   btn.addClass("disabled");
                }
            }
        });

        //Virtual Block
        //Duplicate Block
        $(".ss-gridfield-link-block select[name='Block']").entwine({
             onadd: function() {
                 this.update();
             },
             onchange: function() {
                 this.update();
             },
             update: function() {
                 var btn = this.parents(".ss-gridfield-link-block").find(".btn-link-block");
                 var link = btn.data("href");
                 var block = btn.parents(".ss-gridfield-link-block").find("select[name='Block']").val();
                 if(block && block > 0) {
                    btn.removeClass("disabled");
                    var path = window.location.pathname;
                    //if not in page but in parent block
                    if (path.indexOf("/edit/show/") == - 1){
                        var urlsegment = path.substr(path.indexOf("/edit/EditForm/") + 15);
                        var page = urlsegment.substr(0, urlsegment.indexOf("/"));
                    }
                    else{
                        var page = path.substr(path.indexOf("/edit/show/") + 11);
                    }
                    
                    var finallink = link.replace("{id}", block).replace("{pageid}",page);
                    btn.getGridField().showDetailView(finallink);
                }
                else{
                   btn.addClass("disabled");
                }
            }
        });
    });
})(jQuery);