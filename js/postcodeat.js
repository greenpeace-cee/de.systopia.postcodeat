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

    CRM.api3('PostcodeAT', 'getatstate', {'plznr': postcode_field.val(), 'ortnam': city_field.val()},
        {success: function(data) {
            if (data.is_error == 0 && data.count == 1) {
                var id = data.id;
                var obj = data.values[id];
                var state = data.values[id][0].state;
                cj('#address_' + blockId + '_state_province_id').select2('data', {
                    id: id,
                    text: state
                });
            }

        }
    });
}

function postcodeat_autofill(blockId, currentField) {
    const postcodeField = cj(`#address_${blockId}_postal_code`);
    const cityField = cj(`#address_${blockId}_city`);
    const streetField = cj(`#address_${blockId}_street_address`);

    const [postcode, city, street] = [postcodeField, cityField, streetField].map(f => f.val());

    postcodeat_find_address_matches({
        select: ["plznr", "gemnam38", "ortnam", "zustort", "stroffi"],
        where: { postcode, city, street },
    }).then((results) => {
        if (results.length < 1) return;

        const uniquePostcodes = new Set();
        const uniqueCities = new Set();
        const uniqueStreets = new Set();

        for (const { plznr, gemnam38, ortnam, zustort, stroffi } of results) {
            uniquePostcodes.add(plznr);
            uniqueCities.add(gemnam38).add(ortnam).add(zustort);
            uniqueStreets.add(stroffi);
        }

        if (currentField !== 0 && uniquePostcodes.size === 1) {
            postcodeField.val(Array.from(uniquePostcodes).at(0));
        }

        if (currentField !== 1 && uniqueCities.size === 1) {
            cityField.val(Array.from(uniqueCities).at(0));
        }

        if (currentField !== 2 && uniqueStreets.size === 1) {
            streetField.val(Array.from(uniqueStreets).at(0));
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

function postcodeat_find_address_matches({ select, where }) {
    if (Object.values(where).every(str => str.length < 1)) {
        return Promise.resolve([]);
    }

    const { postcode, city, street } = where;

    return CRM.api4("PostcodeAT", "get", {
        select,
        where: [
            ["plznr", "LIKE", `${postcode}%`],
            ["OR", [
                ["gemnam38", "LIKE", `%${city}%`],
                ["ortnam",   "LIKE", `%${city}%`],
                ["zustort",  "LIKE", `%${city}%`],
            ]],
            ["stroffi", "LIKE", `%${street}%`],
        ],
        groupBy: select,
        limit: 100,
    });
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
