async function clearCache(path, clearVarnish, link) {
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
    if (clearVarnish) {
        console.log("should do varnish purge");
        // todo check if it works when varnish is configured
        // jQuery.ajax({
        //     url: link,
        //     type: 'PURGE',
        //     success: function (result) {
        //         console.log(result) // todo show feedback
        //     },
        //     error: function (error) {
        //         console.error(error)
        //     }
        // })
    } else {
        console.log("do not purge");
    }
}

console.log(wp_ajax);
