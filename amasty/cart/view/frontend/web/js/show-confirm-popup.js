define([
    'jquery'
], function ($) {
    'use strict';

    function confirm(params) {
        var wrapper = $('#confirmBox, #confirmOverlay');
        if (wrapper.length > 0) {
            wrapper.remove();
        }
        var buttonHTML = '',
            checkoutButton = params.checkout ? params.checkout : '',
            value;
        $.each(params.buttons,function(name,obj) {
            value = obj['name'];
            if (obj['timer']) {
                value += obj['timer'];
            }
            // Generating the markup for the buttons:
            buttonHTML += '<button class="' + 'button ' + obj['class'] + '" title="' + obj['name'] + '">' + value + '</button>';
            if (!obj.action) {
                obj.action = function() {};
            }
        });
        var confirmOverlay = $('<div></div>', {
            id: "confirmOverlay"
        });
        var confirmBox = $('<div></div>', {
            id: "confirmBox",
            class: 'amcart-confirm-block'
        });

        switch(params.align) {
            case "1":
                confirmOverlay.addClass('am-top');
                break;
            case "2":
                confirmOverlay.addClass('am-top-left');
                break;
            case "3":
                confirmOverlay.addClass('am-top-right');
                break;
            case "4":
                confirmOverlay.addClass('am-left');
                break;
            case "5":
                confirmOverlay.addClass('am-right');
                break;
            case "0":
            default:
                confirmOverlay.addClass('am-center');
        }
        confirmOverlay.hide().appendTo($('body'));
        var cross = $('<span title="' + $.mage.__("Close") + '" class="cross"></span>').html('&times;');
        cross.on('click', function (e) {
            confirmHideOnClick(e);
        });
        confirmBox.append(cross);
        var confirmButtons = $('<div></div>', {
            id: "confirmButtons",
            class: 'amcart-confirm-buttons'
        });
        confirmButtons.html(buttonHTML + checkoutButton);
        confirmButtons.appendTo(confirmBox);
        var messageBox = $('<div></div>', {
            id: "messageBox",
            class: 'amcart-message-box'
        });
        messageBox.html(params.message);
        messageBox.insertBefore(confirmButtons);
        var relatedBox = $('<div></div>', {
            class: "am-related-box"
        });
        relatedBox.html(params.related);
        relatedBox.insertAfter(confirmButtons);
        confirmBox.hide().appendTo(confirmOverlay).fadeIn();
        confirmOverlay.fadeIn();
        confirmOverlay.on('click', function (e) {
            confirmHideOnClick(e);
        });
        var buttons = $('#confirmButtons button'),
            i = 0;
        $.each(params.buttons,function(name,obj) {
            buttons.eq(i++).on('click', function () {
                obj.action();
                return false;
            });
        });
        confirmTimer();
    }

    function confirmTimer() {
        var elem= $('#confirmButtons .timer'),
            value = elem.text(),
            sec = parseInt(value.replace(/\D+/g,""));
        if (sec) {
            document.timer = setInterval(function() {
                oneSec();
            }, 1000);

            $(".am-btn-right").on('click', function () {
                clearInterval(document.timer);
            });
        }
    }

    //run every second while time !=0
    function oneSec() {
        var elem= $('#confirmButtons .timer'),
            value = elem.text(),
            sec = parseInt(value.replace(/\D+/g,""));
        if (sec) {
            value =  value.replace(sec, sec-1);
            elem.text(value);
            if (sec <= 1) {
                clearInterval(document.timer);
                elem.trigger('click');
            }
        } else {
            clearInterval(document.timer);
        }
    }

    function confirmHideOnClick(event) {
        if ((!$(event.target).parents('#confirmBox').length && !$(event.target).is('.swatch-option'))
            || $(event.target).is('.cross')) {
            confirmHide();
        }
    }

    function confirmHide() {
        $('#confirmBox, #confirmOverlay').fadeOut(function() {
            $(this).remove();
        });
        clearInterval(document.timer);
    }

    return function (response, amCartWidget) {
        confirm({
            'title'      : response.title,
            'message'    : response.message,
            'related'    : response.related,
            'checkout'   : response.checkout,
            'cart'       : response.cart,
            'buttons'    : {
                '1' : {
                    'name'  :  response.b1_name,
                    'class' : 'am-btn-left',
                    'timer' : response.timer,
                    'action': function() {
                        if (response.b1_action.indexOf('document.location') > -1
                            && window.parent.location != window.location
                        ) {
                            response.b1_action = response.b1_action.replace('document.location', 'window.parent.location');
                        }
                        eval(response.b1_action);
                    }
                },
                '2' : {
                    'name'  :  response.b2_name,
                    'class' : 'am-btn-right',
                    'action': function() {
                        if (response.b2_action.indexOf('document.location') > -1
                            && window.parent.location != window.location
                        ) {
                            response.b2_action = response.b2_action.replace('document.location', 'window.parent.location');
                        }
                        eval(response.b2_action);
                    }
                }
            },
            'align': response.align
        });
    };
});
