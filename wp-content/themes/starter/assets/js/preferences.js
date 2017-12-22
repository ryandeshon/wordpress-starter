(function($) {
    var PreferenceTest = function(element) {
        $.extend(this, window.Newsletter.call(this, element));

        this.subscribed = [];

        var self = this,
            preferenceCheckbox = $('.js-pref'),
            subscribeCheckbox = $('.js-sub'),
            fields = $('.js-field'),
            email = $('.js-email'),
            unsub = $('input[name="unsubscribe"]'),

            state = {
                checked : [],
                fieldsChanged : [],
                email: null
            },

            pushEmail = function() {
                state.email = $(this).val();
            },

            pushCheckbox = function() {
                if( $.inArray(this, state.checked)  === -1) {
                    state.checked.push(this);
                }
            },

            pushFields = function() {
                if( $.inArray(this, state.fieldsChanged)  === -1) {
                    state.fieldsChanged.push(this);
                }
            },

            removeAllNewsletters = function() {
                var checked = $(this).is(':checked');
                if(checked) {
                    self.subscribed = [];
                    $('input.js-sub').each(function(i, el) {
                        if($(el).is(':checked')) {
                            self.subscribed.push($(el));
                        }
                    });
                    $('input.js-sub').removeAttr('checked');
                } else {
                    $.each(self.subscribed, function(i, el) {
                        el.prop('checked', true);
                    })

                }

            };

        this.form = $(element).find('form[name="preference"]');

        this.formSubmission = function(e) {

            if(self.clicked) {
                return;
            }

            var unsubscribed;

            self.clicked = true;
            self.errorEl.hide();
            self.confirmation.hide();

            var listOfSubscribed = {};
            $('.js-sub').each(function(el, i) {
                var checked = $(this).is(':checked') ? '1' : '0';
                var val = $(this).val();
                listOfSubscribed[val] = checked;
            });

            if( $('.js-sub:checked').length <= 0 ) {
                unsubscribed = true;
            }

            $.each(state.fieldsChanged, function(i, el) {
                var name = $(this).attr('id');
                var val = $(this).val();
                state.fieldsChanged[i] = name + '=' + val;
            });

            var ajaxurl = self.form.find('input[name="ajaxurl"]').val();
            var nonce = self.form.find('input[name="nonce"]').val();
            var guid = self.form.find('input[name="guid"]').val();

            var data = {
                email: state.email,
                oldEmail: state.oldEmail,
                nonce: nonce,
                action: 'preference_center',
                guid: guid,
                refurl: encodeURI(document.location.href),
                subscriptions: listOfSubscribed,
                preferences: state.checked,
                fields: state.fieldsChanged,
                unsubscribed: unsubscribed
            };
            self.submitForm(ajaxurl, data);

        };

        this.init = function() {
            state.oldEmail = this.form.find('#Email').val();
            preferenceCheckbox.on('change', pushCheckbox);
            fields.on('change', pushFields);
            email.on('change', pushEmail);
            unsub.on('click', removeAllNewsletters);

            var validator = new FormValidator( 'preference',
                [
                    { name: 'Email', rules: 'valid_email'},
                    { name: 'FirstName', rules: 'alpha', display : 'First Name'},
                    { name: 'Bm', rules: 'numeric|max_length[2]'},
                    { name: 'Bd', rules: 'numeric|max_length[2]'},
                    { name: 'State', rules: 'alpha|max_length[2]'},
                    { name: 'Pc', rules: 'alpha_numeric'}
                ],
                function(errors, event) {
                    if( errors.length > 0 ) {
                        $.each(errors, function(i, err) {
                            $(err.element).next('.help-block').html(err.message);
                        })
                    } else {
                        event.preventDefault();
                        self.formSubmission();
                    }
                })

        };
    };

    if(document.getElementById('pc-wrapper')) {
        var preference = new PreferenceTest(document.getElementById('pc-wrapper'));
        preference.init();
    }
})(jQuery);