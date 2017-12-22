(function($) {
    window.Newsletter = function(element) {

        on_form_submission_error = function( jqXHR, textStatus, errorThrown ) {
            self.clicked = false;
            var message = 'OOPS! Email subscription is currently not working. Please try again later.';
            display_error(message);
            self.form.css('opacity', 1);
        },

            on_form_submission_success = function( data, textStatus, jqXHR ) {

                var pidObj = JSON.parse(data);
                if(pidObj.redirect) {
                    self.display_error('You have been unsubscribed from all newsletters and will be redirected back to Read It Forward.');
                    setTimeout(function(){
                        window.location.replace(pidObj.redirect.url);
                    }, 2000);
                } else {
                    if(pidObj.Pid){
                        var rifPid_Pid = pidObj.Pid.Pid;
                        var rifPid_Subs = pidObj.Pid.Subs;
                        utag.link({ "pid": rifPid_Pid, "newsletter_name": rifPid_Subs, "event_type":"newsletter_signup"});
                    }
                    if( !document.getElementById('pc-wrapper') ) {
                        self.form.hide();
                    }

                    self.display_confirmation();
                }

            };

        this.clicked = false;

        this.display_confirmation = function() {
            self.clicked = false;
            self.form.css('opacity', 1);
            self.errorEl.hide();
            self.confirmation.show();
        };

        this.display_error = function(errors) {
            self.clicked = false;
            self.form.css('opacity', 1);
            self.errorEl.html(errors);
            self.errorEl.show();
        };

        this.formSubmission = function(e) {
            self.form.css('opacity', 0.5);

            if(self.clicked) {
                return;
            }

            self.clicked = true;
            var errors;

            var email = self.form.find('input[name="Email"]').val();

            if(errors) {
                self.display_error(errors);
                return;
            }

            var ajaxurl = self.form.find('input[name="ajaxurl"]').val();
            var nonce = self.form.find('input[name="nonce"]').val();
            var action = self.form.find('input[name="action"]').val();

            var data = {
                email: email,
                nonce: nonce,
                action: action,
                refurl: encodeURI(document.location.href)
            };


            self.submitForm(ajaxurl, data);
        };

        this.submitForm = function(url, data) {
            self.form.css('opacity', 0.4);
            $.ajax( {
                url: url,
                data: data,
                success: on_form_submission_success,
                error: on_form_submission_error
            } );
        };


        this.form = $(element).find('form[name="subscribe"]');
        this.errorEl = $(element).find('.js-error');
        this.confirmation = $(element).find('.js-success');
        var self = this;

        this.init = function() {
            var validator = new FormValidator( 'subscribe',
                [
                    { name: 'Email', rules: 'valid_email|required'}
                ],
                function(errors, event) {
                    if( errors.length > 0 ) {
                        $.each(errors, function(i, err) {
                            self.errorEl.html(err.message);
                            self.errorEl.show();
                        })
                    } else {
                        event.preventDefault();
                        self.errorEl.hide();
                        self.formSubmission();
                    }
                })

        }

        return this;
    };

    $('.js-subscribe').each(function(index, element) {
        var newsletterInstance = new window.Newsletter(element);
        newsletterInstance.init();
    });
})(jQuery);