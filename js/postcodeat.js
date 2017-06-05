/*-------------------------------------------------------+
 | SYSTOPIA - Postcode Lookup for Austria                 |
 | Copyright (C) 2017 SYSTOPIA                            |
 | Author: M. Wire (mjw@mjwconsult.co.uk)                 |
 |         B. Endres (endres@systopia.de)                 |
 | http://www.systopia.de/                                |
 +--------------------------------------------------------+
 | This program is released as free software under the    |
 | Affero GPL license. You can redistribute it and/or     |
 | modify it under the terms of this license which you    |
 | can read by viewing the included agpl.txt or online    |
 | at www.gnu.org/licenses/agpl.html. Removal of this     |
 | copyright header is strictly prohibited without        |
 | written permission from the original author(s).        |
 +--------------------------------------------------------*/

/*
 * Function to retrieve the postcode and fill the fields
 */
function postcodeat_setstateprovince(blockId, postcode) {
    //check if country is AT.
    if ((cj('#address_' + blockId + '_country_id').val()) != 1014) {
        return;
    }

    //run only when a postcode is present
    //if (postcode.length != 4) {
//        return;
//    }

    var postcode_field = cj('#address_'+blockId+'_postal_code');
    var city_field = cj('#address_'+blockId+'_city');
    var street_field = cj('#address_'+blockId+'_street_address');

    CRM.api3('PostcodeAT', 'getatstate', {'sequential': 1, 'plznr': postcode_field.val(), 'ortnam': city_field.val(), 'stroffi': street_field.val()},
        {success: function(data) {
            if (data.is_error == 0 && data.count == 1) {
                var obj = data.values[0];
                var id = data.values[0].id;
                var state = data.values[0].state;
                cj('#address_' + blockId + '_state_province_id').select2('data', {
                    id: id,
                    text: state
                });
            }

        }
    });
}

function postcodeat_autofill(blockId, currentField) {
    var postcode_field = cj('#address_'+blockId+'_postal_code');
    var city_field = cj('#address_'+blockId+'_city');
    var street_field = cj('#address_'+blockId+'_street_address');

    cj.ajax( {
        url: CRM.url('civicrm/ajax/postcodeat/autocomplete'),
        dataType: "json",
        data: {
            mode: 1,
            plznr: postcode_field.val(),
            ortnam: city_field.val(),
            stroffi: street_field.val(),
        },
        success: function( data ) {
            var plznr = data[0].plznr;
            var ortnam = data[0].ortnam;
            var stroffi = data[0].stroffi;

            if (currentField != 0) postcode_field.val(plznr);
            if (currentField != 1) city_field.val(ortnam);
            if (currentField != 2) street_field.val(stroffi);
        }
    });
}

function postcodeat_init_addressBlock(blockId, address_table_id) {
    var postcode_field = cj('#address_'+blockId+'_postal_code');
    var city_field = cj('#address_'+blockId+'_city');
    var street_field = cj('#address_'+blockId+'_street_address');

    postcode_field.focusout(function(e) {
        postcodeat_autofill(blockId, 0);
        postcodeat_setstateprovince(blockId, postcode_field.val());
    });

    city_field.focusout(function(e) {
        postcodeat_autofill(blockId, 1);
        postcodeat_setstateprovince(blockId, postcode_field.val());
    });

    street_field.focusout(function(e) {
        postcodeat_autofill(blockId, 2);
        postcodeat_setstateprovince(blockId, postcode_field.val());
    });

    // Get fields and add Ids so we can move them around
    var postcodesuffix = cj('#address_'+blockId+'_postal_code_suffix');
    var city_td = cj('#address_'+blockId+'_city').parents('td:first');
    var city_tr_inner = city_td.parents('tr:first').attr('id', 'cityAddressTrInner_'+blockId);

    // Add an Id to the postalcode td
    cj('#address_'+blockId+'_postal_code').parents('td:first').attr('id','postcodeAddress_'+blockId);
    var postalcode_td = cj('#postcodeAddress_'+blockId);

    // Add an Id to the Country TR
    cj('#address_' + blockId + '_country_id').parents('tr:first').parents('tr:first').attr('id','countryAddressTr_'+blockId);
    var country_tr = cj('#countryAddressTr_'+blockId);

    cj('#address_' + blockId + '_country_id').change(function(e) {
        if ((cj('#address_' + blockId + '_country_id').val()) == 1014) {
            // Rearrange fields so postcode is on first line, city on second line
            postalcode_td.insertBefore('#streetAddress_'+blockId).wrap('<tr id="postcodeAddressTr_'+blockId+'"></tr>');
            postcodesuffix.hide();
            cj('label[for=address_'+blockId+'_postal_code]').text("ZIP Code"); // Postcode label
            cj('label[for=address_1_postal_code]').next().hide(); // Hide "Suffix" label
            postcodesuffix.next().hide(); // Hide "Suffix" help
            city_td.insertAfter('#postcodeAddressTr_'+blockId).wrap('<tr id="cityAddressTr_'+blockId+'"></tr>');

        } else {
            // Reset to default layout (city, postcode on same line above country)
            cj('#postcodeAddressTr_'+blockId).remove();
            cj('#cityAddressTr_'+blockId).remove();
            city_tr_inner.children('td:last').hide();
            city_td.appendTo(city_tr_inner).show();
            postalcode_td.appendTo(city_tr_inner).show();
            postcodesuffix.show();
            cj('label[for=address_'+blockId+'_postal_code]').text("Zip / Postal Code"); // Postcode label
            cj('label[for=address_1_postal_code]').next().show(); // Hide "Suffix" label
            postcodesuffix.next().show(); // Hide "Suffix" help
        }
    });

    cj('#address_' + blockId + '_country_id').trigger('change');
}

cj(function() {
    cj.each(['show', 'hide'], function (i, ev) {
        var el = cj.fn[ev];
        cj.fn[ev] = function () {
          this.trigger(ev);
          return el.apply(this, arguments);
        };
      });
});
