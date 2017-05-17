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
    var first_row = cj(address_table_id + ' tbody tr:first');
    first_row.before('<tr class="hiddenElement postcodeat_input_row" id="postcodeat_row_' + blockId + '"><td>Postleitzahl<br /><input class="crm-form-text" id="postcodeat_postcode_' + blockId + '" /></td></tr>');

    var postcode_field = cj('#postcodeat_postcode_' + blockId);
    var postalcode_td = cj('#address_'+blockId+'_postal_code').parent();
    var supplemental1_field = cj('#address_'+blockId+'_supplemental_address_1');

    supplemental1_field.removeClass('huge');
    supplemental1_field.addClass('six');
    cj('label[for=address_'+blockId+'_supplemental_address_1]').text("Hausnummer");

    //supplementary1_field.label('Haus');

    postcode_field.change(function(e) {
        cj('#address_' + blockId + '_postal_code').val(postcode_field.val());
        postcodeat_retrieve(blockId, postcode_field.val());
    });

    postcode_field.keyup(function(e) {
        cj('#address_' + blockId + '_postal_code').val(postcode_field.val());
        postcodeat_retrieve(blockId, postcode_field.val());
    });

    cj('#address_' + blockId + '_country_id').ready(function(e) {
        if ((cj('#address_' + blockId + '_country_id').val()) == 1014) {
            var first_row = cj(address_table_id + ' tbody tr:first');
            first_row.before('<tr class="hiddenElement postcodeat_input_row" id="postcodeat_row_' + blockId + '"><td>Postleitzahl<br /><input class="crm-form-text" id="postcodeat_postcode_' + blockId + '" /></td></tr>');
            cj('#postcodeat_row_' + blockId).show();
        }
    });

    cj('#address_' + blockId + '_country_id').change(function(e) {
        if ((cj('#address_' + blockId + '_country_id').val()) == 1014) {
            cj('#postcodeat_row_' + blockId).show();
            postalcode_td.hide();
            postcode_field.val(cj('#address_' + blockId + '_postal_code').val());
        } else {
            cj('#postcodeat_row_' + blockId).hide();
            postalcode_td.show();
        }
    });

    cj('#address_' + blockId + '_country_id').trigger('change');
}

/**
 * remove all the input elements for postcodes
 */
function postcodeat_reset() {
    cj('.postcodeat_input_row').remove();
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
