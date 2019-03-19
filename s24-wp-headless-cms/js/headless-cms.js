async function clearCache(path) {
    var feedback = document.getElementById("cache-result");
    var data = {
        'action': 's24_headless_cms_clear_cache',
        'url': path,
        '_ajax_nonce': wp_ajax._ajax_nonce
    };
    jQuery.post(wp_ajax.ajaxurl, data, function (response) {
        console.log(response);

        feedback.innerText = response.message + " (" + response.reason + ")";
        feedback.setAttribute("style", "display: inline-block;");
    });
}

console.log(wp_ajax);
