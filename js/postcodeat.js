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
function postcodeat_retrieve(blockId, postcode) {

    //check if country is AT.
    if ((cj('#address_' + blockId + '_country_id').val()) != 1014) {
        return;
    }

    //run only when a postcode is present
    if (postcode.length != 4) {
        return;
    }

    CRM.api3('PostcodeAT', 'getatstate', {'sequential': 1, 'plznr': postcode},
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

function postcodeat_init_addressBlock(blockId, address_table_id) {
    var postcode_field = cj('#address_'+blockId+'_postal_code');
    var postalcode_td = cj('#address_'+blockId+'_postal_code').parent();
    var postcodesuffix = cj('#address_'+blockId+'_postal_code_suffix');

    postcode_field.change(function(e) {
        postcodeat_retrieve(blockId, postcode_field.val());
    });

    postcode_field.keyup(function(e) {
        postcodeat_retrieve(blockId, postcode_field.val());
    });

    postcode_field.click(function(e) {
        postcodeat_retrieve(blockId, postcode_field.val());
    });

    cj('#address_' + blockId + '_country_id').change(function(e) {
        if ((cj('#address_' + blockId + '_country_id').val()) == 1014) {
            cj('#address_'+blockId+'_postal_code').parent().insertBefore('#streetAddress_'+blockId).wrap('<tr id="postcodeAddress_'+blockId+'"></tr>');
            postcodesuffix.hide();
            cj('label[for=address_'+blockId+'_postal_code]').text("Postleitzahl"); // Postcode label
            cj('label[for=address_1_postal_code]').next().hide(); // Hide "Suffix" label
            postcodesuffix.next().hide(); // Hide "Suffix" help

        } else {
            cj('#address_'+blockId+'_postal_code').parent().insertAfter('#address_'+blockId+'_city').unwrap('<tr id="postcodeAddress_'+blockId+'"></tr>');
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
