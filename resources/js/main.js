function showToast(message, status = 'bg-success') {
    $('#toast-component').remove();
    var toast = `<div id="toast-component" class="position-fixed top-0 end-0 p-3" style="z-index: 1051">
                        <div id="myToast" class="toast hide ${status} bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-body d-flex justify-content-between">
                            ${escapeHtml(message)}
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>`;
    $('body').append(toast);
    (new bootstrap.Toast($('#myToast'))).show();
}

function escapeHtml(unsafe) {
    return unsafe.toString().replace(/[&<"']/g, function(m) {
        switch (m) {
            case '&':
                return '&amp;';
            case '<':
                return '&lt;';
            case '"':
                return '&quot;';
            default:
                return '&#039;';
        }
    });
}

function replaceUrl(datas) {
    let rootUrl = window.location.href.split('?')[0];
    if (rootUrl.includes('#')) {
        let indexHash = rootUrl.indexOf('#');
        rootUrl = rootUrl.slice(0, indexHash);
    }
    let queryString = Object.keys(datas)
        .map(key => {
            return `${key}=${encodeURIComponent(datas[key])}`;
        })
        .join('&');

    return `${rootUrl}?${queryString}`;
}

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return '';
}

