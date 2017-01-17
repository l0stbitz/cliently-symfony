/**
 Address editable input.
 Internally value stored as {address_line1: 'adddd', address_line2: 'bbbdddd', city: "Moscow", state: "Lenina", zip: "332323", country: 'United State'}
 
 @class address
 @extends abstractinput
 @final
 @example
 <a href="#" id="address" data-type="address" data-pk="1">awesome</a>
 <script>
 $(function(){
 $('#address').editable({
 url: '/post',
 title: 'Enter city, street and building #',
 value: {
 address_line1: 'adddd',
 address_line2: 'bbbdddd',
 city: "Moscow",
 state: "Lenina", 
 zip: "332323", 
 country: 'United State'
 
 }
 });
 });
 </script>
 **/
(function ($) {
    "use strict";

    var Address = function (options) {
        this.init('address', options, Address.defaults);
    };

    //inherit from Abstract input
    $.fn.editableutils.inherit(Address, $.fn.editabletypes.abstractinput);

    $.extend(Address.prototype, {
        /**
         Renders input from tpl
         
         @method render() 
         **/
        render: function () {
            this.$input = this.$tpl.find('input');
        },
        /**
         Default method to show value in element. Can be overwritten by display option.
         
         @method value2html(value, element) 
         **/
        value2html: function (value, element) {
            if (!value) {
                $(element).empty();
                return;
            }
            var html = '';
            if (typeof value.address_line1 != 'undefined' && value.address_line1) {
                if (html) {
                    html += ', ';
                }
                html += $('<div>').text(value.address_line1).html();
            }
            if (typeof value.address_line2 != 'undefined' && value.address_line2) {
                if (html) {
                    html += ', ';
                }
                html += $('<div>').text(value.address_line2).html();
            }
            if (typeof value.city != 'undefined' && value.city) {
                if (html) {
                    html += ', ';
                }
                html += $('<div>').text(value.city).html();
            }
            if (typeof value.state != 'undefined' && value.state) {
                if (html) {
                    html += ', ';
                }
                html += $('<div>').text(value.state).html();
            }
            if (typeof value.zip != 'undefined' && value.zip) {
                if (html) {
                    html += ', ';
                }
                html += $('<div>').text(value.zip).html();
            }
            if (typeof value.country != 'undefined' && value.country) {
                if (html) {
                    html += ', ';
                }
                html += $('<div>').text(value.country).html();
            }
            //var html = $('<div>').text(value.city).html() + ', ' + $('<div>').text(value.street).html() + ' st., bld. ' + $('<div>').text(value.building).html();
            $(element).html(html);
        },
        /**
         Gets value from element's html
         
         @method html2value(html) 
         **/
        html2value: function (html) {
            /*
             you may write parsing method to get value by element's html
             e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
             but for complex structures it's not recommended.
             Better set value directly via javascript, e.g. 
             editable({
             value: {
             city: "Moscow", 
             street: "Lenina", 
             building: "15"
             }
             });
             */
            return null;
        },
        /**
         Converts value to string. 
         It is used in internal comparing (not for sending to server).
         
         @method value2str(value)  
         **/
        value2str: function (value) {
            var str = '';
            if (value) {
                for (var k in value) {
                    str = str + k + ':' + value[k] + ';';
                }
            }
            return str;
       },
        /*
         Converts string to value. Used for reading value from 'data-value' attribute.
         
         @method str2value(str)  
         */
        str2value: function (str) {
            /*
             this is mainly for parsing value defined in data-value attribute. 
             If you will always set value by javascript, no need to overwrite it
             */
            return str;
        },
        /**
         Sets value of input.
         
         @method value2input(value) 
         @param {mixed} value
         **/
        value2input: function (value) {
            if (!value) {
                return;
            }
            this.$input.filter('[name="address_line1"]').val(value.address_line1);
            this.$input.filter('[name="address_line2"]').val(value.address_line2);
            this.$input.filter('[name="city"]').val(value.city);
            this.$input.filter('[name="state"]').val(value.state);
            this.$input.filter('[name="zip"]').val(value.zip);
            this.$input.filter('[name="country"]').val(value.country);
        },
        /**
         Returns value of input.
         
         @method input2value() 
         **/
        input2value: function () {
            return {
                address_line1: this.$input.filter('[name="address_line1"]').val(),
                address_line2: this.$input.filter('[name="address_line2"]').val(),
                city: this.$input.filter('[name="city"]').val(),
                state: this.$input.filter('[name="state"]').val(),
                zip: this.$input.filter('[name="zip"]').val(),
                country: this.$input.filter('[name="country"]').val()
            };
        },
        /**
         Activates input: sets focus on the first field.
         
         @method activate() 
         **/
        activate: function () {
            this.$input.filter('[name="address_line1"]').focus();
      },
        /**
         Attaches handler to submit form in case of 'showbuttons=false' mode
         
         @method autosubmit() 
         **/
        autosubmit: function () {
            this.$input.keydown(function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        }
    });

    Address.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl: '<div class="editable-address row"><div class="col-md-12"><input type="text" name="address_line1" placeholder="Address Line 1" class="form-control input-small"></div></div>' +
                '<div class="editable-address row"><div class="col-md-12"><input type="text" name="address_line2" placeholder="Address Line 2" class="form-control input-small"></div></div>' +
                '<div class="editable-address row"><div class="col-md-6"><input type="text" name="city" placeholder="City" class="form-control input-small"></div><div class="col-md-6"><input type="text" name="state" placeholder="State" class="form-control input-small"></div></div>' +
                '<div class="editable-address row"><div class="col-md-6"><input type="text" name="zip" placeholder="ZIP" class="form-control input-small"></div><div class="col-md-6"><input type="text" name="country" placeholder="Country" class="form-control input-small"></div></div>',
        inputclass: 'address-container'
    });

    $.fn.editabletypes.address = Address;

}(window.jQuery));