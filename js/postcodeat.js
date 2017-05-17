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
    var supplemental1_field = cj('#address_'+blockId+'_supplemental_address_1');

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
            supplemental1_field.removeClass('huge');
            supplemental1_field.addClass('six');
            postcodesuffix.hide();
            cj('label[for=address_'+blockId+'_supplemental_address_1]').text("Hausnummer"); // Supp address1 label
            cj('label[for=address_'+blockId+'_supplemental_address_1]').next().hide(); // Hide help
            cj('label[for=address_'+blockId+'_supplemental_address_2]').text("Postfach"); // Supp address2 label
            supplemental1_field.removeClass('huge');
            supplemental1_field.addClass('big');
            cj('label[for=address_'+blockId+'_postal_code]').text("Postleitzahl"); // Postcode label
            cj('label[for=address_1_postal_code]').next().hide(); // Hide "Suffix" label
            postcodesuffix.next().hide(); // Hide "Suffix" help
        } else {
            supplemental1_field.removeClass('six');
            supplemental1_field.addClass('huge');
            postcodesuffix.show();
            cj('label[for=address_'+blockId+'_supplemental_address_1]').text("Supplementary Address 1"); // Supp address1 label
            cj('label[for=address_'+blockId+'_supplemental_address_1]').next().show(); // Hide help
            cj('label[for=address_'+blockId+'_supplemental_address_2]').text("Supplementary Address 2"); // Supp address2 label
            supplemental1_field.removeClass('big');
            supplemental1_field.addClass('huge');
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
