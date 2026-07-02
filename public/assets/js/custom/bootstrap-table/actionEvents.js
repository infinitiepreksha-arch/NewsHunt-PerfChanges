
window.customFieldValueEvents = {
    'click .edit_btn': function (e, value, row) {
        $("#new_custom_field_value").val(row.value);
        $("#old_custom_field_value").val(row.value);
    }
}

window.itemEvents = {
    'click .editdata': function (e, value, row) {
        let html = `<table class="table">
            <tr>
                <th width="10%">${trans("No.")}</th>
                <th width="25%" class="text-center">${trans("Image")}</th>
                <th width="25%">${trans("Name")}</th>
                <th width="40%">${trans("Value")}</th>
            </tr>`;
        $.each(row.custom_fields, function (key, value) {
            html += `<tr class="mb-2">
                <td>${key + 1}</td>
                <td class="text-center">
                <a class="image-popup-no-margins" href="${value.image}" >
                <img src=${value.image} height="30px" width="30px" style="border-radius:8px;" alt="" onerror="onErrorImage(event)">
                </a>
                </td>
                <td>${value.name}</td>`;

            if (value.type == "fileinput") {
                if (value.value != undefined) {
                    if (value.value?.value.match(/\.(jpg|jpeg|png|svg)$/i)) {
                        html += `<td><img src="${value.value?.value}" alt="Custom Field Files" class="w-25" onerror="onErrorImage(event)"></td>`
                    } else {
                        html += `<td><a target="_blank" href="${value.value?.value}">View File</a></td>`
                    }

                } else {
                    html += `<td></td>`
                }
            } else {
                html += `<td class="text-break">${value.value?.value || ''}</td>`
            }

            html += `</tr>`;
        });

        html += "</table>";
        $('#custom_fields').html(html)
    },

    'click .edit-status': function (e, value, row) {
        $('#status').val(row.status).trigger('change');
        $('#rejected_reason').val(row.rejected_reason);
    }
}


window.advertisementPackageEvents = {
    'click .edit_btn': function (e, value, row) {
        $('#edit_name').val(row.name);
        $('#edit_price').val(row.price);
        $('#edit_discount_in_percentage').val(row.discount_in_percentage);
        $('#edit_final_price').val(row.final_price);
        $("#edit_duration").val(row.duration);
        $('#edit_durationLimit').val(row.duration);
        $('#edit_ForLimit').val(row.item_limit);
        $('#edit_description').val(row.description);
        $('#edit_ios_product_id').val(row.ios_product_id);
    }
};

window.reportReasonEvents = {
    'click .edit_btn': function (e, value, row) {
        $("#edit_reason").val(row.reason);
    }
}

window.staffEvents = {
    'click .edit_btn': function (e, value, row) {
        $('#edit_role').val(row.roles[0].id);
        $('#edit_name').val(row.name);
        $('#edit_email').val(row.email);
    }
}

window.userEvents = {
    'click .assign_package': function (e, value, row) {
        $("#user_id").val(row.id);
        $('.package_type').prop('checked', false);

        $('#item-listing-package-div').hide();
        $('#advertisement-package-div').hide();

        $('#advertisement-package').attr('required', false);
        $('#item-listing-package').attr('required', false);

        $('#package_details').hide();
        $('.payment').hide();
        $('.cheque').hide();
    }
}

window.areaEvents = {
    'click .edit_btn': function (e, value, row) {
        $('#edit_name').val(row.name);
    }
}
