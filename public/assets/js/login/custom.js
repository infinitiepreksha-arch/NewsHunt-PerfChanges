"use strict";

function togglePassword() {
    const passwordField = document.getElementById('password');
    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
}

$('#bg_image').on('error', function () {
    this.src = $(this).data('custom-image');
    $('.login_bg').css('background-image', "url(" + $(this).data('custom-image') + ")");
});
$('#company_logo').on('error', function () {
    this.src = $(this).data('custom-image');
});

$(document).ready(function () {
    $('#admin-btn').on('click', function () {
        $('#email').val('admin@gmail.com');
        $('#password').val('12345678');
    })
});
