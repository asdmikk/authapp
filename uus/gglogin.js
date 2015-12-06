function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function() {
        console.log('User signed out from google');
    });
}

function logout() {
    gapi.auth.signOut();
    location.reload();
}

function login() {
    var myParams = {
        'clientid': '107351278458-2ham2g0jg4ijs2noa9fsoanqul3kllva.apps.googleusercontent.com',
        'cookiepolicy': 'single_host_origin',
        'callback': 'loginCallback',
        'approvalprompt': 'force',
        'scope': 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read'
    };
    gapi.auth.signIn(myParams);
}

function loginCallback(result) {
    if (result['status']['signed_in']) {
        var request = gapi.client.plus.people.get({
            'userId': 'me'
        });
        request.execute(function(resp) {
            var email = '';
            if (resp['emails']) {
                for (i = 0; i < resp['emails'].length; i++) {
                    if (resp['emails'][i]['type'] == 'account') {
                        email = resp['emails'][i]['value'];
                    }
                }
            }
            var p_data = {
                source: "google",
                u_id: user_u_id,
                name: resp.displayName,
                img_url: resp.image.url,
                email: email,
                id: resp.id
            };
            $.post("../save.py", p_data)
                .done(function() {
                    location.reload();
                });
        });
    }
}

function onLoadCallback() {
    gapi.client.setApiKey('AIzaSyBoFiZK06AsbEjc_188g1C3C3D4lGqzLx0');
    gapi.client.load('plus', 'v1', function() {});
}

(function() {
    var po = document.createElement('script');
    po.type = 'text/javascript';
    po.async = true;
    po.src = 'https://apis.google.com/js/client.js?onload=onLoadCallback';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
})();
