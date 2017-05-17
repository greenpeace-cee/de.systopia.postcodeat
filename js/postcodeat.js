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

    first_row.before('<tr class="hiddenElement postcodeat_input_row" id="postcodeat_row_' + blockId + '"><td>Postcode<br /><input class="crm-form-text" id="postcodeat_postcode_' + blockId + '" /></td></tr>');

    var postcode_field = cj('#postcodeat_postcode_' + blockId);
    var street_number_td = cj('#address_'+blockId+'_street_number').parent();
    var street_name_td = cj('#address_'+blockId+'_street_name').parent();
    var street_unit_td = cj('#address_'+blockId+'_street_unit').parent();
    var postalcode_td = cj('#address_'+blockId+'_postal_code').parent();

    postcode_field.change(function(e) {
        cj('#address_' + blockId + '_postal_code').val(postcode_field.val());
        postcodeat_retrieve(blockId, postcode_field.val());
    });

    postcode_field.keyup(function(e) {
        cj('#address_' + blockId + '_postal_code').val(postcode_field.val());
        postcodeat_retrieve(blockId, postcode_field.val());
    });

    cj('#address_' + blockId + '_country_id').change(function(e) {
        if ((cj('#address_' + blockId + '_country_id').val()) == 1014) {
            if (typeof processAddressFields == 'function' && cj('#addressElements_' + blockId).length > 0) {
            }
            cj('#postcodeat_row_' + blockId).show();
            //street_number_td.hide();
            //street_unit_td.hide();
            postalcode_td.hide();

            postcode_field.val(cj('#address_' + blockId + '_postal_code').val());
        } else {
            cj('#postcodeat_row_' + blockId).hide();
            street_number_td.show();
            street_unit_td.show();
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
