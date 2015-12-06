$(document).ready(function() {
    populate();
});

$('#logoutbutton').click(function() {
    signOut();
    fb_logout();
    window.location = '../';
});

function populate() {
    console.log("populating");
    $.get('../api/api.py', {u_id: user_u_id})
        .success(function(data) {
            console.log("api call successful");
            $('#id_data_container #name').text(data.persons[0].name);
            $('#id_data_container #id_code').text(data.persons[0].id_code);

            if (data.persons[0].gg_id === null) {
                console.log("ggid null");
                $('#gg_data_container').text('Not connected');
            } else {
                $('#gg_data_container #picture').html('<img src="' + data.persons[0].gg_img + '" />');
                $('#gg_data_container #name').html(data.persons[0].gg_name);
                $('#gg_data_container #id').html(data.persons[0].gg_id);
                $('#gg_data_container #email').html(data.persons[0].email);
            }
            if (data.persons[0].fb_id === null) {
                console.log("fbid null");
                $('#fb_data_container').text('Not connected');
            } else {
                $('#fb_data_container #picture').html('<img src="' + data.persons[0].fb_img + '" />');
                $('#fb_data_container #name').html(data.persons[0].fb_name);
                $('#fb_data_container #id').html(data.persons[0].fb_id);
                $('#fb_data_container #email').html(data.persons[0].fb_email);
            }
        });
}
